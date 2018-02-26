<?php
/**
* User_Controller class
* using global namespaces
*/
class User_Controller
{
    /**
     * get all headers values
     * @return array
     */
    private function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    /**
     * this is login process for user and all protected api call
     * @param $data
     * @return bool|string
     */
    public function login($data)
    {
        try  {
            // fetch header auth values need send from client
            foreach ($this->getallheaders() as $name => $value) {
                // secret value of auth signature
                if ($name == 'X-Auth-Secret') {
                    $secret = $value;
                }

                // token value of auth signature
                if ($name == 'X-Auth-Token') {
                    $token = $value;
                }
            }

            // chekc login credentials
            if (isset($data->username) && isset($data->password) && isset($token) && isset($secret)) {
                $user = new User();
                $data->token = $token;
                $data->secret = $secret;
                $valid = $user->login($data);

                if ($valid != false) {
                    return date('d-m-Y H:i:s', $valid);
                }
            } else {
                return -1;
            }

            return false;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * verify the signature [token+secret] with valid time session
     * @return array|bool
     */
    public function verify()
    {
        try  {
            // fetch header auth values
            foreach ($this->getallheaders() as $name => $value) {
                if ($name == 'X-Auth-Secret') {
                    $secret = $value;
                }

                if ($name == 'X-Auth-Token') {
                    $token = $value;
                }
            }

            if (isset($token) && isset($secret)) {
                $user = new User();
                $valid = $user->verifySignature($token, $secret);
                return $valid;
            }

            return false;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}