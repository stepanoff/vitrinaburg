<?php
class CustomFacebookService extends FacebookOAuthService {
	
    protected function fetchAttributes() {
        $info = (object) $this->makeSignedRequest('https://graph.facebook.com/me');

        $this->attributes['id'] = $info->id;
        $this->attributes['name'] = $info->name;
        $this->attributes['username'] = $info->name;
        $this->attributes['url'] = $info->link;
        $this->attributes['avatar'] = '';
        $this->attributes['photo'] = '';
        $this->attributes['email'] = '';
        $this->attributes['gender'] = '';
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