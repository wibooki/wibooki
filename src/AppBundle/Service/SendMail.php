<?php

namespace AppBundle\Service;

class SendMail {

    public function __construct($mailer,$templating) {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * Función que que sirve para enviar emails sin tener que estar ejecutando siempre las plantillas
     * y la inicialización de mailer
     *
     * @param string $receiver Email destinatario
     * @param string $subject Asunto del email
     * @param string $template Template que se utilizará para enviar el email AppBundle:Mail:<template>.html.twig
     * @param array $content Array que almacena variables que se utilizan en el template
     * @param mixed $attach
     * @return mixed
     */
    public function send ($receiver, $subject, $template, array $content = [], $from, $attach = null) {
        $htmlBody = $this->templating->render('mail/'.$template.'.html.twig',$content);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setContentType("text/html")
            ->setTo($receiver)
            ->setBody($htmlBody)
        ;
        return $this->mailer->send($message);
    }
}