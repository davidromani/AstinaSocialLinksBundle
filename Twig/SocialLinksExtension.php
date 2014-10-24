<?php

namespace Astina\Bundle\SocialLinksBundle\Twig;

use SocialLinks\Page;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

/**
 * Class SocialLinksExtension
 *
 * @author    Drazen Peric <dperic@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
class SocialLinksExtension extends \Twig_Extension
{
    /**
     * @var FragmentHandler
     */
    private $handler;

    /**
     * @param FragmentHandler $handler
     */
    public function __construct(FragmentHandler $handler)
    {
        $this->handler = $handler;
    }

    public function getFunctions()
    {
        return array(
            'social_link' => new \Twig_Function_Method($this, 'getSocialLink', array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string $provider
     * @param string $url
     * @param array  $settings
     *
     * @return \InvalidArgumentException|string
     */
    public function getSocialLink($provider, $url, $settings = array())
    {
        if (!$url) {
            throw new \InvalidArgumentException('Url for social links extension is not provided.');
        }

        $page = new Page(array(
            'url'   => $url,
            'title' => isset($settings['title']) ? $settings['title'] : null,
            'text'  => isset($settings['text']) ? $settings['text'] : null
        ));

        if (!$page->$provider) {
            throw new \InvalidArgumentException(sprintf('Provider `%s does not exist in social links extension.', $provider));
        }

        $reference = new ControllerReference('AstinaSocialLinksBundle:SocialLinks:socialLink', array(
            'data' => array(
                'socialUrl' => $page->$provider->shareUrl,
                'target'    => isset($settings['target']) ? $settings['target'] : '_blank',
                'image'     => isset($settings['image']) ? $settings['image'] : null,
                'class'     => isset($settings['class']) ? $settings['class'] : null
            )
        ));

        return $this->handler->render($reference);
    }

    public function getName()
    {
        return 'social_links';
    }
}
