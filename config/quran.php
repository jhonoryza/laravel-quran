<?php

return [
    /**
     * Source of quran.
     * options: 'kemenag', 'tanzil.net'
     */
    'source' => ENV('QURAN_SOURCE', 'kemenag'),

    /**
     * Base URI of quran
     * kemenag: https://web-api.qurankemenag.net/
     * tanzil.net:
     */
    'base_uri' => ENV('QURAN_BASE_URI', 'https://web-api.qurankemenag.net/'),

    /**
     * Audio Base URI of quran
     * kemenag: https://media.qurankemenag.net/audio/Abu_Bakr_Ash-Shaatree_aac64/
     * tanzil.net: https://everyayah.com/data/Husary_128kbps/
     * other source: https://www.versebyversequran.com/
     */
    'audio_base_uri' => ENV('QURAN_AUDIO_BASE_URI', 'https://media.qurankemenag.net/audio/Abu_Bakr_Ash-Shaatree_aac64/'),

    /**
     * Quran api connect and request timeout
     */
    'timeout' => ENV('QURAN_TIMEOUT', 10),
];
