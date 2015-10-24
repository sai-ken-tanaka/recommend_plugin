<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Plugin\Recommend;

use Eccube\Event\RenderEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form as Error;
use Eccube\Common\Constant;

class Recommend
{

    private $app;

    /**
     * コンストラクタ.
     * @param unknown $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    // フロント：商品詳細画面におすすめ商品を表示
    public function showRecommendProduct(FilterResponseEvent $event)
    {
        $app = $this->app;
        $id = $app['request']->attributes->get('id');
        $Product = $app['eccube.repository.product']->find($id);
        $RecommendProducts = $app['eccube.plugin.recommend.repository.recommend_product']->findAll();
        if (count($RecommendProducts) > 0) {
            $twig = $app->renderView(
                'Recommend/View/recommend_product.twig',
                array(
                    'RecommendProducts' => $RecommendProducts,
                )
            );
            $response = $event->getResponse();
            $html = $response->getContent();
            $crawler = new Crawler($html);
            $oldElement = $crawler
                ->filter('#main');
            $oldHtml = $oldElement->html();
            $newHtml = $oldHtml.$twig;
            $html = $crawler->html();
            $html = str_replace($oldHtml, $newHtml, $html);
            $response->setContent($html);
            $event->setResponse($response);
        }
    }	

}
