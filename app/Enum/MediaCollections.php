<?php

namespace App\Enum;

enum MediaCollections: string
{
    case AVATAR = 'avatar';
    case DOCUMENTS = 'documents';
    case LICENSE = 'license';
    case SERVICE = 'services';
    case CATEGORY = 'category';
    case PRODUCT = 'products';
    case BANNER = 'banners';
    case ARTICLE = 'articles';
    case NEWS = 'news';

    /**
     * Get media collection name without language suffix
     */
    public function getWithoutLang(): string
    {
        // Remove _spanish or _english suffix
        return preg_replace('/_(?:es|en)$/i', '', $this->value);
    }
}
