<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Libslim\Parsers;

/**
 * Handlebars view
 *
 * The Handlebars view is a custom View class that renders templates using the Handlebars
 * template language (https://github.com/mardix/Handlebars).
 *
 */
class Hndbars extends \Slim\View {

    /**
     * @var Handlebars The Handlebars engine for rendering templates.
     */
    private $parserInstance = null;
    private $allowedOptions = array(
        'templateExtension',
        'partialsDirectory'
    );
    private static $helpers;
    private static $blocks;
    
    private $templateExtension=[];

    /**
     * @param array [$options]
     * @return void
     */
    public function __construct($options = array()) {

        parent::__construct();

        foreach (array_intersect_key($options, array_flip($this->allowedOptions)) as $key => $value) {

            $this->$key = $value;
        }

        $helpers = [
            'url' => function($arg1, $arg2, $options = null) {
        //echo " url<br>";
        //var_dump($arg1, $arg2,$options);

        if (!$arg1)
            return '/emptyargs';

        if ($arg1[0] == 'image')
            $arg1[0] = 'img';

        $file = '/' . $arg1[0] . '/' . current($arg2);
        
        if($arg1[0]=='js'){
            return $arg2['js_src'];
        }

        if ($arg1[0] == 'cdn') {

            $uri = $arg1[1];
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
            }


            return 'http://cdn.cdngozilla.com/' . $f;
        }


        if (isset($arg2['css_src'])) {
            $file =  $arg1[0] . '/' . $arg2['css_src'];
        } else {
            if ($arg1[0] == 'singlefile') {
                $file = "should include as direct JS ." . $arg2['file_src'];
                $file = '/js/' . $arg2['file_src'];
            }
        }

        return $file;
    },
            'def' => function($arg1, $arg2) {
        if ($arg1[0])
            return $arg1[0];
        return $arg2['def'];
        //echo " def<br>";
        //var_dump($arg1, $arg2);
        return $arg1 ? $arg1 : $arg2;
    },
            'ifeval' => function ($conditional, $options) {
        return false;
        //echo "<br> ifeval $conditional <br>";
        if ($conditional) {
            return $options['fn']();
        } else {
            return $options['inverse']();
        }
    }
        ];

        self::$helpers = $helpers;

        self::$blocks = [
            'ifevalNOTUSED' => function($cx, $args, $named) {
        echo " ifeval<br>";

        //var_dump($args);
        //var_dump($named);
        return '/somefakeurl/' . $arg1[0];
    }
        ];
    }

    /**
     * Render Handlebars Template
     *
     * This method will output the rendered template content
     *
     * @param   string $template The path to the Handlebars template, relative to the templates directory.
     * @param null $data
     * @return  void
     */
    public function render($template, $data = null) {

        $partialsDirectory = '/' . trim($this->getTemplatesDirectory(), '/');
        $template = str_replace('.', '/', $template) . '.html';
        $path = $partialsDirectory . '/' . $template;

        $cachefile = $partialsDirectory . '/cache/' . md5($path);
        if (file_exists($cachefile)) {
            //    $renderer = include($cachefile);
            //  return $renderer($this->all()); 
        }

        $phpStr = \LightnCandy::compile(file_get_contents($path), Array(
                    'flags' => \LightnCandy::FLAG_HANDLEBARSJS | \LightnCandy::FLAG_RUNTIMEPARTIAL | \LightnCandy::FLAG_ERROR_EXCEPTION,
                    'helpers' => self::$helpers,
                    'blockhelpers' => self::$blocks,
                    'basedir' => Array(
                        $partialsDirectory,
                        $partialsDirectory . '/_extends',
                    ),
                    'fileext' => $this->templateExtension
        ));

        
        $cachefile = $partialsDirectory . '/cache/' . md5($path);
        file_put_contents($cachefile, $phpStr);

        $renderer = include($cachefile);
        //var_dump($phpStr);
        //var_dump($cachefile);
        //exit();
        return $renderer($this->all());
        //return $parser->render($this->all());
    }

    /**
     * Creates new Handlebars Engine if it doesn't already exist, and returns it.
     *
     * @return \Handlebars
     */
    public function getInstanceKKK() {
        if (!$this->parserInstance) {

            $partialsDirectory = $this->getTemplatesDirectory() . "/partials";
            $options = array();

            if (isset($this->templateExtension)) {

                $options['extension'] = $this->templateExtension;
            }

            if (isset($this->partialsDirectory)) {

                $partialsDirectory = $this->partialsDirectory;
            }

            if (!is_dir(rtrim(realpath($partialsDirectory), '/'))) {

                throw new \RuntimeException("Partials directory '{$partialsDirectory}' is not a valid directory.");
            }




            $templatesLoader = new \Handlebars\Loader\FilesystemLoader($this->getTemplatesDirectory(), $options);
            $partialsLoader = new \Handlebars\Loader\FilesystemLoader($partialsDirectory, $options);

            $this->parserInstance = new \Handlebars\Handlebars([ "loader" => $templatesLoader, "partials_loader" => $partialsLoader]);
        }

        return $this->parserInstance;
    }

}
