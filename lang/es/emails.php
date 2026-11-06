<?php

// The wording of the emails, by the reason they are sent. See App\Emails\BlogEmails.
return [
    'brand' => 'IRSO BLOG',
    'tagline' => 'Ideas, historias y lo que te inspira',
    'greeting' => 'Hola :name,',
    'greeting_anonymous' => '¡Hola!',
    'sign_off' => '— El equipo de IRSO BLOG',
    'link_hint' => 'Si el botón no funciona, copia y pega este enlace en tu navegador:',

    'footer' => [
        'visit' => 'Visitar el blog',
        'account' => 'Recibes este correo porque tienes una cuenta en IRSO BLOG. Es un mensaje automático, no es necesario responderlo.',
        'author' => 'Recibes este aviso porque escribiste esta publicación en IRSO BLOG. Es un mensaje automático, no es necesario responderlo.',
        'requested' => 'Recibes este correo porque alguien escribió esta dirección para entrar a IRSO BLOG. Es un mensaje automático, no es necesario responderlo.',
        'security' => 'Nunca te pediremos tu contraseña ni tus códigos de verificación por correo.',
    ],

    'label' => [
        'account' => 'Cuenta',
        'date' => 'Fecha',
        'publication' => 'Publicación',
        'link_expires' => 'El enlace vence en',
        'code_expires' => 'El código vence en',
        'minutes' => ':minutes minutos',
    ],

    'reset_password' => [
        'kicker' => 'Seguridad de tu cuenta',
        'subject' => 'Restablece tu contraseña de IRSO BLOG',
        'preheader' => 'Elige una contraseña nueva para volver a entrar.',
        'title' => 'Elige una contraseña nueva',
        'body' => [
            'Recibimos una solicitud para restablecer la contraseña de tu cuenta. Con el siguiente botón puedes elegir una nueva y volver a entrar.',
        ],
        'action' => 'Elegir contraseña nueva',
        'callout_title' => '¿No fuiste tú?',
        'callout' => 'Puedes ignorar este correo: tu contraseña seguirá siendo la misma.',
    ],

    'social_login_code' => [
        'kicker' => 'Verificación',
        'subject' => 'Tu código de verificación: :code',
        'preheader' => 'Escríbelo en IRSO BLOG para terminar de entrar.',
        'title' => 'Este es tu código para entrar',
        'body' => [
            'Escríbelo en la página donde lo pediste para confirmar tu correo y terminar de entrar a IRSO BLOG.',
        ],
        'highlight' => 'Tu código',
        'callout_title' => '¿No intentaste entrar?',
        'callout' => 'Puedes ignorar este correo: sin el código nadie puede usar esta dirección.',
    ],

    'welcome' => [
        'kicker' => 'Bienvenida',
        'subject' => 'Te damos la bienvenida a IRSO BLOG',
        'preheader' => 'Tu cuenta está lista: lee, comenta y escribe tu primera publicación.',
        'title' => 'Ya tienes un lugar para contar tus historias',
        'body' => [
            'Tu cuenta está lista. Esto es lo que puedes hacer desde hoy:',
        ],
        'steps' => [
            ['title' => 'Lee a la comunidad', 'text' => 'Encuentra las historias destacadas y lo último de cada categoría.'],
            ['title' => 'Conversa', 'text' => 'Comenta las publicaciones que te hagan pensar.'],
            ['title' => 'Escribe la tuya', 'text' => 'Crea tu primera publicación con vista previa en vivo.'],
        ],
        'action' => 'Explorar el blog',
        'secondary' => 'Escribir mi primera publicación',
    ],

    'password_changed' => [
        'kicker' => 'Seguridad de tu cuenta',
        'subject' => 'Tu contraseña de IRSO BLOG cambió',
        'preheader' => 'Si fuiste tú, no hace falta que hagas nada.',
        'title' => 'Tu contraseña se actualizó',
        'body' => [
            'La contraseña de tu cuenta se cambió correctamente. Si fuiste tú, no hace falta que hagas nada.',
        ],
        'action' => 'Revisar mi cuenta',
        'secondary' => 'Restablecer mi contraseña',
        'callout_title' => '¿No fuiste tú?',
        'callout' => 'Restablece tu contraseña cuanto antes para recuperar el control de tu cuenta.',
    ],

    'comment_received' => [
        'kicker' => 'Comunidad',
        'subject' => ':commenter comentó tu publicación',
        'preheader' => 'Comentario en «:publication».',
        'title' => 'Tu publicación tiene un comentario nuevo',
        'body' => [
            ':commenter dejó un comentario en tu publicación:',
        ],
        'action' => 'Leer y responder',
    ],
];
