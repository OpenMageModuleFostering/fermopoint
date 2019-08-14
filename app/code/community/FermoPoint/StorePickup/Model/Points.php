<?php

class FermoPoint_StorePickup_Model_Points {

    const CACHE_TAG = 'fermopoint_points';
    const CACHE_KEY = 'fermopoint_points_%s';
    const CACHE_LIFETIME = 3900; // 1 hour + 5 minutes to overlap cronjob
    
    protected function _storePoint($data)
    {
        $pointId = $data['id'];
        $point = Mage::getModel('fpstorepickup/point')->load($pointId);
        if ( ! $point->getId())
            $point->setId($pointId);
        $point
            ->setPointData($data)
            ->save()
        ;
        return $point;
    }
    
    public function getPoint($pointId)
    {
        $point = Mage::getModel('fpstorepickup/point')->load($pointId);
        if ( ! $point->getId())
            $point
                ->setId($pointId)
                ->setName('Point #' . $pointId)
                ->save()
            ;
                
        return $point;
    }
    
    public function getPoints(FermoPoint_StorePickup_Model_Api_SearchData $request, $bypassCache = false)
    {
        $needles = $request->toApi();
        $cacheKey = sprintf(self::CACHE_KEY, sha1(implode(',', $needles)));
        $cache = Mage::app()->getCache();
        $value = unserialize($cache->load($cacheKey));
        if ($bypassCache || ! is_array($value) || ! $request->compare($value))
        {
            $points = Mage::getSingleton('fpstorepickup/api')->getPoints($needles);
            $value = $needles;
            $value['points'] = array();
            foreach ($points as $point)
                $value['points'][] = $this->_storePoint($point)->toArray();
            $cache->save(serialize($value), $cacheKey, array(self::CACHE_TAG), self::CACHE_LIFETIME);
        }
        return $value['points'];
    }

}
