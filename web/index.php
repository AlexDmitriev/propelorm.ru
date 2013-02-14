<?php

class Document
{
    private $title;
    private $layout;
    private $text;

    public function __construct($name)
    {
        $name = str_replace(".", '', $name);
        $path = '../markdown/'.$name.'.markdown';

        if (!is_file($path))
        {
            $path = "../markdown/404.markdown";
        }

        $raw = file($path);

        $this->setLayout(trim(str_replace('layout:', "", $raw[1])));
        $this->setTitle(trim(str_replace('title:', "", $raw[2])));
        $this->setText(implode("", array_slice($raw, 4)));
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setText($text)
    {
        $this->text = Markdown($text);
    }

    public function getText()
    {
        return $this->text;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }
}

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;


$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig');
});

$app->get('/documentation/', function() use ($app) {
    $document = new Document('documentation');
    return $app['twig']->render($document->getLayout() . '.twig',
        array(
            "html"  => $document->getText(),
            "title"  => $document->getTitle()
        )
    );
});

$app->get('/documentation/{slug}.html', function($slug) use ($app) {
    $document = new Document('documentation/'.$app->escape($slug));
    return $app['twig']->render($document->getLayout() . '.twig',
        array(
            "html"  => $document->getText(),
            "title"  => $document->getTitle(),
        )
    );
});

$app->run();
