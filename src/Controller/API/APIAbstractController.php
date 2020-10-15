<?php

namespace App\Controller\API;

use App\Lib\Pager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class APIAbstractController extends AbstractController
{
    protected function getResult($result, ?Pager $pager = null)
    {
        $result = [
            'data' => $result,
        ];
        if ($pager) {
            $result['page'] = $pager->getPage();
            $result['per_page'] = $pager->getLimit();
        }

        return $this->json($result);
    }
}