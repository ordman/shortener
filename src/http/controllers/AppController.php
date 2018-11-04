<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04.11.18
 * Time: 15:21
 */

namespace http\controllers;


use Fleshgrinder\Validator\URL;
use http\Router;
use Illuminate\Database\Capsule\Manager;
use models\ShortUrl;
use Routing\Request;
use Smarty;
use utils\Converter;
use utils\ConverterException;

class AppController
{

    /** @var Smarty $smarty */
    private $smarty;

    /** @var Router $router */
    private $router;

    /** @var Request $request */
    private $request;

    public function __construct(Router $router, Smarty $smarty)
    {
        $this->smarty = $smarty;
        $this->router = $router;
        $this->request = $router->getRequest();

    }

    public function homeAction($page = 0, $errorMsg = '')
    {
        $perPage = 5;
        $query = Manager::table('short_urls')
            ->where('is_deleted', '=', 0)
            ->orderBy('updated_at', 'DESC');

        $countPages = ceil($query->count()/$perPage);
        $urls = $query->forPage($page + 1, $perPage)->get();

        if ($page > 0 && !$urls) {
            $this->error404Action();
        } else {
            $converter = new Converter();
            foreach ($urls as &$url) {
                $url['short'] = $this->request->getHTTPHost() . '/' . $converter->dec2link($url['id']);
            }

            $this->smarty->assign('title', 'Short link service');
            $this->smarty->assign('error', $errorMsg);
            $this->smarty->assign('urls', $urls);
            $this->smarty->assign('countPages', $countPages);
            $this->smarty->assign('page', $page + 1);
            $this->smarty->display('index.tpl');
        }

    }

    public function redirectAction($page)
    {
        $converter = new Converter();

        try {
            $id = $converter->link2dec($page);
        } catch (ConverterException $e) {
            $this->error404Action();
            exit;
        }

        $shortUrl = ShortUrl::firstByAttributes(array('id' => $id,'is_deleted' => 0));
        if (!is_null($shortUrl)) {
            header('HTTP/1.0 301 Moved Permanently');
            header('Location: '.$shortUrl->url);
            exit;
        }

        $this->error404Action();
    }

    public function error404Action()
    {
        header('HTTP/1.0 404 Not Found');
        $this->smarty->assign('title', '404. Page not found!');

        $this->smarty->display('404.tpl');
    }

    private function deleteShortLink($id)
    {
        $shortUrl = ShortUrl::firstByAttributes(array('id' => $id));

        $shortUrl->deleted_at = date('Y-m-d H:i:s');
        $shortUrl->is_deleted = 1;
        $shortUrl->update();
    }

    private function createShortLink()
    {
        try{
            $url = new URL(trim($this->request->get('full_url')));
            $fullUrl = $url->__toString();
        } catch (\InvalidArgumentException $e) {
            $this->homeAction(0,$e->getMessage());
            exit;
        }

        $hash = md5($fullUrl);

        // update if exist same
        $shortUrl = ShortUrl::firstByAttributes(array('hash' => $hash,'is_deleted' => 0));

        if (is_null($shortUrl)) { // try to use deleted id for optimization
            $shortUrl = ShortUrl::firstByAttributes(array('is_deleted' => 1));
        }

        $shortUrl = !is_null($shortUrl) ? $shortUrl : new ShortUrl();

        $shortUrl->updated_at = date('Y-m-d H:i:s');
        $shortUrl->is_deleted = 0;
        $shortUrl->url = $fullUrl;
        $shortUrl->hash = $hash;


        if ($shortUrl->id) {
            $shortUrl->update();
        } else {
            $shortUrl->save();
        }
    }


    public function createOrDeleteShortLink()
    {

        if (!is_null($id = $this->request->get('delete'))) {
            $this->deleteShortLink($id);
        } else {
            $this->createShortLink();
        }

        $this->homeAction();
    }
}