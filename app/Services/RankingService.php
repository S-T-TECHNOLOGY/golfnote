<?php


namespace App\Services;


class RankingService
{
    public function getRanking($params)
    {
        $type =  isset($params['type']) ? $params['type'] : 'friend';
    }
}