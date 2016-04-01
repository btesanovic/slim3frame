<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart
 * @author      Andrew Smith
 * @link        http://www.slimframework.com
 * @copyright   2013 Josh Lockhart
 * @version     0.1.3
 * @package     SlimViews
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Libslim\Views;

//use Slim\Slim;


class HelpersExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'libslim';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('url', array($this, 'url')),            
        );
    }

    public function url($kind,$uri){
        switch ($kind){
        case 'js':
            return $this->base().'/js/'.$uri;
            break;
        case 'css':
            return $this->base().'/css/'.$uri;
            break;
        case 'cdn': 
            $parts = explode('/', $uri);
            $f='';
            if ($parts[0] == 'js') {
                $f = '/bower_components/' . $parts[1] . '/dist/' . $parts[2];
                //var_dump($f);
                //readfile($f);
            } elseif ($parts[0] == 'css') {
                $f = '/bower_components/' . $parts[1] . '/dist/css/' . $parts[2];
                //var_dump($f);
                //readfile($f);
            }else{
                $f = '/bower_components/' . $uri;
            }


            return 'http://cdn.cdngozilla.com' . $f;
            break;
        default : break;
        }
        return $this->base().'/'.$uri;
    }

    public function urlFor($name, $params = array(), $appName = 'default')
    {
        return Slim::getInstance($appName)->urlFor($name, $params);
    }

    public function site($url, $withUri = true, $appName = 'default')
    {
        return $this->base($withUri, $appName) . '/' . ltrim($url, '/');
    }

    public function base($withUri = true, $appName = 'default')
    {
        $c = \A::c();
        $req = $c['request'];
        $uri = $req->getUri();

        if ($withUri) {
            //$uri .= $req->getRootUri();
            //$uri.=$withUri;
        }
        return $uri;
    }

    public function currentUrl($withQueryString = true, $appName = 'default')
    {
        $c = \A::c();
        $req = $c['request'];
        $uri = $req->getUrl() . $req->getPath();

        if ($withQueryString) {
            $env = $c['environment'];

            if ($env['QUERY_STRING']) {
                $uri .= '?' . $env['QUERY_STRING'];
            }
        }

        return $uri;
    }
}
