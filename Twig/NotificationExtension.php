<?php

namespace Mgilet\NotificationBundle\Twig;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Mgilet\NotificationBundle\Entity\NotifiableEntity;
use Mgilet\NotificationBundle\Entity\Notification;
use Mgilet\NotificationBundle\Manager\NotificationManager;
use Mgilet\NotificationBundle\NotifiableInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Twig_Extension;

/**
 * Twig extension to display notifications
 **/
class NotificationExtension extends Twig_Extension
{
    protected $notificationManager;
    protected $storage;
    protected $twig;
    protected $router;

    /**
     * NotificationExtension constructor.
     * @param NotificationManager $notificationManager
     * @param TokenStorage $storage
     * @param \Twig_Environment $twig
     */
    public function __construct(NotificationManager $notificationManager, TokenStorage $storage, \Twig_Environment $twig, Router $router)
    {
        $this->notificationManager = $notificationManager;
        $this->storage = $storage;
        $this->twig = $twig;
        $this->router = $router;
    }

    /**
     * @return array available Twig functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('mgilet_notification_render', array($this, 'render'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('mgilet_notification_count', array($this, 'countNotifications'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('mgilet_notification_unseen_count', array($this, 'countUnseenNotifications'), array(
                'is_safe' => array('html')
            ))
        );
    }

    /**
     * Rendering notifications in Twig
     *
     * @param array               $options
     * @param NotifiableInterface $notifiable
     *
     * @return null|string
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function render(NotifiableInterface $notifiable, array $options = array())
    {
        if (!array_key_exists('seen', $options)) {
            $options['seen'] = true;
        }

        return $this->renderNotifications($notifiable, $options);
    }

    /**
     * Render notifications of the notifiable as a list
     *
     * @param NotifiableInterface   $notifiable
     * @param array                 $options
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function renderNotifications(NotifiableInterface $notifiable, array $options)
    {
        $order = array_key_exists('order', $options) ? $options['order'] : null;

        if ($options['seen']) {
            $notifications = $this->notificationManager->getNotifications($notifiable, $order);
        } else {
            $notifications = $this->notificationManager->getUnseenNotifications($notifiable, $order);
        }

        // if the template option is set, use custom template
        $template = array_key_exists('template', $options) ? $options['template'] : '@MgiletNotification/notification_list.html.twig';

        return $this->twig->render($template,
            array(
                'notificationList' => $notifications
            )
        );
    }

    /**
     * Display the total count of notifications for the notifiable
     *
     * @param NotifiableInterface $notifiable
     *
     * @return int
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function countNotifications(NotifiableInterface $notifiable)
    {
        return $this->notificationManager->getNotificationCount($notifiable);
    }

    /**
     * Display the count of unseen notifications for this notifiable
     *
     * @param NotifiableInterface $notifiable
     *
     * @return int
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function countUnseenNotifications(NotifiableInterface $notifiable)
    {
        return $this->notificationManager->getUnseenNotificationCount($notifiable);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mgilet_notification';
    }
}
