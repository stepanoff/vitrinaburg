<?php
interface IVAuthDbDriver
{
    /**
     * Returns an user information array.
     * @return mixed an user information.
     */
    public function findByService($service, $serviceId);
	/**
	 * Returns an user information array.
	 * @return mixed an user information.
	 */
	public function findByToken($token);
    /**
     * Returns an user information array.
     * @return mixed an user information.
     */
    public function findByPk($id);
    /**
     * Update user information.
     * @return bool
     */
    public function updateByPk($id, $data);
	/**
	 * Creates new user and store information in db.
	 * @return user id
	 */
	public function addUser($data);
	/**
	 * Add user token.
	 * @return boolean
	 */
	public function addToken($userId, $token, $expire=0);

    /**
     * Remove user token.
     * @return boolean
     */
    public function removeToken($token);

    /**
     * find user uid by token
     * @return string
     */
    public function findUidByToken ($token);


}
?>