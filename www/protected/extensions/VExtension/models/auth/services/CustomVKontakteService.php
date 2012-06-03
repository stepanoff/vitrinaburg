<?php
class CustomVKontakteService extends VKontakteOAuthService {
	
	// protected $scope = 'friends';
	
	protected function fetchAttributes() {
		$info = (array)$this->makeSignedRequest('https://api.vkontakte.ru/method/getProfiles', array(
			'query' => array(
				'uids' => $this->uid,
				//'fields' => '', // uid, first_name and last_name is always available
				'fields' => 'uid, first_name, last_name, screen_name, sex, photo, photo_medium, photo_big, photo_rec',
			),
		));

		$info = $info['response'][0];

		$this->attributes['id'] = $info->uid;
		$this->attributes['name'] = $info->first_name.' '.$info->last_name;
        $this->attributes['username'] = $info->first_name.' '.$info->last_name;
		$this->attributes['url'] = 'http://vkontakte.ru/id'.$info->uid;
        $this->attributes['avatar'] = $info->photo;
        $this->attributes['photo'] = $info->photo_big;
        $this->attributes['email'] = '';
        $this->attributes['gender'] = $info->sex == 1 ? 'F' : 'M';
	}

    public function getDuration ()
    {
        $expires = $this->getState('expires');
        if ($expires)
        {
            $duration = $expires -time();
            if ($duration && $duration>0)
                return $duration;
        }
        return 0;
    }
}
