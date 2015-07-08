<?php

/**
 * Fetch data from altcoin/bitcoin deamon
 *
 * @author Lukas Mestan
 * @copyright GPL v2
 * @version 1.0.2
 */
class DaemonData  
{
    /**
     * Wallet IP
     *
     * @access protected
     * @var string
     */
    protected $ip = '';
    /**
     * Wallet port
     *
     * @access protected
     * @var integer|string
     */
    protected $port = '';
    /**
     * Wallet username
     *
     * @access protected
     * @var string
     */
    protected $username = '';
    /**
     * Wallet password
     *
     * @access protected
     * @var string
     */
    protected $password = '';


    /**
     * Class contructor
     *
     * @access public
     * @param string $ip
     * @param integer $port
     * @param string $username
     * @param string $password
     */
    public function __construct($ip, $port, $username, $password)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get data from JSON RPC by method & params
     *
     * @access public
     * @param string $method
     * @param array $params
     * @return array
     */
    public function get_data($method, array $params = array())
    {
        return $this->fetch_data(array(
            'method' => $method,
            'params' => $params
        ));
    }

    /**
     * Fetch JsonRPC request information from the deamon
     *
     * @access private
     * @param array $request
     * @return array
     * @see error codes https://github.com/bitcoin/bitcoin/blob/master/src/rpcprotocol.h#L34
     * @throws Exception
     */
    private function fetch_data($request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->ip);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-type: application/json"
        ));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        $info = json_decode($response, TRUE);
        if ( ! empty($info["error"]) )
        {
            throw new Exception($info["error"]["message"] . " (Error Code: " . $info["error"]["code"] . ")");
        }

        return $info["result"];
    }

}
