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

            'folder' => 'testfolder',

            'clientEmail' => getenv('BACKUP_GDRIVE_EMAIL'),

            'privateKeyFile' => null,

            // optional, fill privateKeyFile OR privateKeyContent (usefull for tests in travis)
            'privateKeyContent' => base64_decode(getenv('BACKUP_GDRIVE_PRIVATE_KEY_CONTENT')),

            'privateKeyPassword' => getenv('BACKUP_GDRIVE_PRIVATE_KEY_PASS'),
        )
    )
);