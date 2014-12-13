<?php
return array(
    'databases' => array(
        'default'
    ),
    'directories' => array(
        public_path()
    ),
    'storages' => array(
        array(
            'type' => 'gdrive',

            'clientEmail' => getenv('BACKUP_GDRIVE_EMAIL'),

            'privateKeyFile' => getenv('BACKUP_GDRIVE_PRIVATE_KEY_FILE'),

            // optional, fill privateKeyFile OR privateKeyContent (usefull for tests in travis)
            'privateKeyContent' => base64_decode(getenv('BACKUP_GDRIVE_PRIVATE_KEY_CONTENT')),

            'privateKeyPassword' => getenv('BACKUP_GDRIVE_PRIVATE_KEY_PASS'),
        )
    )
);