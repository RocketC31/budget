<?php

return [
    'secret_id' => env("BANK_SYNC_SECRET_ID", false),
    'secret_key' => env("BANK_SYNC_SECRET_KEY", false),
    'available' => env("BANK_SYNC_SECRET_ID", false) && env("BANK_SYNC_SECRET_KEY", false),
    'ai_api' => env("BANK_SYNC_AI_API_KEY"),
];
