<?php

namespace zembrowski\SMS;

/**
 * Class Orange
 * @package zembrowski\phpsms-poland
 */
class Orange
{

    public $url = 'https://www.orange.pl'; // orange.pl URL
    private $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/603.2.4 (KHTML, like Gecko) Version/10.1.1 Safari/603.2.4';
    private $login_request_uri = '/zaloguj.phtml'; // login form request uri
    private $login_post_query_string = '?_DARGS=/ocp/gear/infoportal/portlets/login/login-box.jsp'; // login form POST query string
    private $send_request_uri = '/portal/map/map/message_box?mbox_view=newsms'; // request uri of form for sending new messages
    private $send_post_request_uri = '/portal/map/map/message_box?_DARGS=/gear/mapmessagebox/smsform.jsp'; // action target for POST request of the sending new messages form
    public $max_length = '640'; // max. length of one SMS message according to the sending new messages form

    /**
     * Session placeholder during the whole execution
     * @var \Requests_Session
     */
    private $session;

    /**
     * Initialized DOM for response analyzing
     * @var \simple_html_dom
     */
    private $html;

    /**
     * Session data variable (not being cross-checked yet)
     * @var string
     */
    private $dynamic_session;

    /**
     * True if user logged in successfully
     * @var boolean
     */
    private $logged_in = false;

    /**
     * Form submission token placeholder
     * @var string
     */
    private $token;

    /**
     * Instantiates the Requests handler with session support.
     */
    public function __construct()
    {
        $session = new \Requests_Session($this->url);
        $session->useragent = $this->user_agent;
        $this->session = $session;
        $this->session->get($this->login_request_uri);

        $html = new \simple_html_dom();
        $this->html = $html;

        $random = rand(1000000000, 2147483647);
        $this->dynamic_session = $random . $random;
    }

    /**
     * Login at orange.pl
     *
     * You have to be register at orange.pl
     * Head to: https://www.orange.pl/rejestracja.phtml
     *
     * @param string $login - login or the number assosciated with the service you use
     * @param string $password - password (pol. "Hasło")
     */
    public function login($login, $password)
    {
        // Referer header set only to act more like a browser
        $this->session->headers['Referer'] = $this->url . $this->login_request_uri;

        $this->session->data = array(
            '_dyncharset' => 'UTF-8',
            '_dynSessConf' => $this->dynamic_session,
            '/tp/core/profile/login/ProfileLoginFormHandler.loginErrorURL' => $this->url . $this->login_request_uri,
            '_D:/tp/core/profile/login/ProfileLoginFormHandler.loginErrorURL' => '',
            '/tp/core/profile/login/ProfileLoginFormHandler.loginSuccessURL' => $this->url . $this->send_request_uri,
            '_D:/tp/core/profile/login/ProfileLoginFormHandler.loginSuccessURL' => '',
            '/tp/core/profile/login/ProfileLoginFormHandler.firstEnter' => 'true',
            '_D:/tp/core/profile/login/ProfileLoginFormHandler.firstEnter' => '',
            '/tp/core/profile/login/ProfileLoginFormHandler.value.login' => $login,
            '_D:/tp/core/profile/login/ProfileLoginFormHandler.value.login' => '',
            '/tp/core/profile/login/ProfileLoginFormHandler.value.password' => $password,
            '_D:/tp/core/profile/login/ProfileLoginFormHandler.value.password' => '',
            '_D:/tp/core/profile/login/ProfileLoginFormHandler.rememberMe' => '',
            '/tp/core/profile/login/ProfileLoginFormHandler.login' => 'Zaloguj się',
            '_D:/tp/core/profile/login/ProfileLoginFormHandler.login' => '',
            '_DARGS' => '/ocp/gear/infoportal/portlets/login/login-box.jsp'
        );

        $response = $this->session->post($this->login_request_uri . $this->login_post_query_string);

        // TODO: Proof, that user logged in (other than token)
        $this->logged_in = true;
        $this->token = $this->token($response->body);

        $result = array(
            'errors' => $this->checkErrors($response->body, 'div.login-box__error p', 'login'), 'remaining' => $this->remaining($response->body)
        );

        return $result;
    }

    /**
     * Retrieves the token from the passed content
     *
     * @param string $content - content to be searched through
     * @return string - token
     */
    private function token($content)
    {

        if ($content) {

            $element = $this->find($content, 'div#box-smsform form input[name=/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.token]', 0);

            if (count($element) > 0) {

                $value = $element->value;

                if (!empty($value)) {

                    $result = $value;

                } else {

                    $result = $this->getToken();

                }

            } else {

                $result = $this->getToken();

            }

        } else {

            $result = $this->getToken();

        }

        return $result;
    }

    /**
     * Send a SMS through the webform at $this->send_post_request_uri
     *
     * @param string $recipient - addressable phone number of the recipient(s)
     *  + 9 digits without leading zero for national mobile numbers
     *    (e.g. 501234567)
     *  + for landline and international numbers plus sign or two leading
     *    zeros followed by international dialing code are allowed
     *    (e.g. 004912345678901 or +4912345678901)
     *  + integer values recommended (or strings with no special chars
     *    except plus sign); spaces seem to get trimmed
     *  + up to five recipients as a comma separeted string are allowed
     *    for one request (e.g. 501234567,004912345678901)
     * @param string $text - content of the SMS
     * @param boolean $multiple - should be true for multiple send requests in
     *                            a session; in case of multiple send()
     *                            function invokes during one session a new
     *                            token for every request has to be retrieved
     *                            (default: false)
     */
    public function send($recipient, $text, $multiple = false)
    {

        $this->checkLoggedIn();

        if (strlen($text) <= 0 || strlen($text) > $this->max_length) {
            throw new \Exception('The message must be longer than 0 characters, but shorter than ' . $this->max_length . ' characters');
        }

        $this->session->options['timeout'] = 30;

        // Referer header set only to act more like a browser
        $this->session->headers['Referer'] = $this->url . $this->send_request_uri;

        $this->session->data = array(
            '_dyncharset' => 'UTF-8',
            '_dynSessConf' => $this->dynamic_session,
            '/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.type' => 'sms',
            '_D:/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.type' => '',
            '/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.errorURL' => '',
            '_D:/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.errorURL' => '',
            '/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.successURL' => $this->send_request_uri,
            '_D:/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.successURL' =>'',
            '/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.to' => $recipient,
            '_D:/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.to' => '',
            '/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.body' => $text,
            '_D:/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.body' => '',
            '/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.token' => $this->token,
            '_D:/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.token' => '',
            'enabled' => false,
            '/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.create.x' => rand(0, 50),
            '/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.create.y' => rand(0, 25),
            '_D:/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.create' => '',
            '_DARGS' => '/gear/mapmessagebox/smsform.jsp'
        );

        $response = $this->session->post($this->send_post_request_uri);

        if ($multiple) $this->token = $this->token($response->body);

        $result = array(
            'status_code' => $response->status_code,
            'errors' => $this->checkErrors($response->body, 'div.box-error p', 'send'),
            'remaining' => $this->remaining($response->body)
        );

        return $result;
    }

    /**
     * Find element in HTML Dom
     *
     * @param string $content - content to be searched through
     * @return object
     */
    private function find($content, $selector, $nth = null)
    {
        $this->html->load($content);

        if (is_int($nth) || $nth === 0) {

            $result = $this->html->find($selector, $nth);

        } else {

            $result = $this->html->find($selector);

        }

        return $result;
    }

    /**
     * Checks the remaining SMS left this month from the response body
     *
     * @param string $content - content to be searched through
     * @return boolean|int|string - SMS remaining this month
     *         false if no content
     *         int if integer value present
     *         string in other cases
     */
    private function remaining($content)
    {

        $this->checkLoggedIn();

        if ($content) {

            $elements = $this->find($content, '#syndication p.item span.value');

            if (count($elements) > 0) {

                $pre_result = $this->checkRemaining($elements);

                if ($pre_result['found']) {

                    $result = $pre_result;

                } else {

                    $result = $this->getRemaining();

                }

            } else {

                $result = $this->getRemaining();

            }

        } else {

            $result = $this->getRemaining();

        }

        return $result;
    }

    /**
     * Get the amount of remaining SMS this month through a request
     *
     * @return boolean|array - false if not logged in, no valuable content
     *  otherwise array result of checkRemaining() function
     */
    public function getRemaining()
    {

        $this->checkLoggedIn();

        $response = $this->session->get($this->send_request_uri);
        $elements = $this->find($response->body, '#syndication p.item span.value');

        if (count($elements) > 0) {

            $result = $this->checkRemaining($elements);

        } else {

            $result = false;

        }

        return $result;
    }

    /**
     * Get the token through a request
     *
     * @return string - token
     */
    public function getToken()
    {

        $this->checkLoggedIn();

        $response = $this->session->get($this->send_request_uri);
        $element = $this->find($response->body, 'div#box-smsform form input[name=/amg/ptk/map/messagebox/formhandlers/MessageFormHandler.token]', 0);

        if (count($element) > 0) {

            $result = $this->token($response->body);
            $this->token = $result;

        } else {

            $result = false;

        }


        return $result;
    }

    /**
     * Check whether errors have been returned
     *
     * @param string $content - response body of a request
     * @return boolean - false if no element described by the selector exists
     */
    private function checkErrors($content, $selector, $function = null)
    {
        $elements = $this->find($content, $selector);
        $message = null;

        if (count($elements) > 0) {

            foreach ($elements as $key => $item) {

                $details = (!empty($function)) ? 'Function ' . $function . ' returned: ' : null;
                $message .= $details . trim($item->plaintext) . '; ';

            }

            throw new \Exception($message);

            $result = true;

        } else {

            $result = false;

        }

        return $result;
    }

    /**
     * Checks whether user logged in
     */
    private function checkLoggedIn() {

        if (!$this->logged_in) {

            throw new \Exception('You are not logged in. Log in first.');

        }

    }

    /**
     * Checks value for the remaining() and getRemaing() functions
     *
     * @param string $elements - input data
     * @return array - information about the retrieved information
     *         boolean 'found' - false if no valuable content (default: false)
     *         int 'remaining' - remaining amount of SMS (default: 0)
     *         array 'errors' - array with errors, key is the index of
     *                          the element with an error
     */
    private function checkRemaining($elements)
    {

        $found = false;
        $count = 0;
        $errors = array();

        foreach ($elements as $key => $item) {

            $value = $item->plaintext;

            if (!empty($value)) {

                $value_int = intval(trim($value));

                if (is_int($value_int)) {

                    $count += $value_int;
                    $found = true;

                } else {

                    $errors[$key] = 'No integer value found for key indexed ' . $key . '. Retrieved value: "' . $value . '"';

                }

            }

        }

        $result = array(
            'found' => $found,
            'count' => $count,
            'errors' => $errors
        );

        return $result;

    }
}
