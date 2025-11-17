<?php
use Kreait\Firebase\Factory;

return [
    'firestore' => fn() => (new Factory())->createFirestore(),
];