<?php

namespace App\Enums;

enum ContentTypeEnum: string
{
    case Text = 'text';
    case Image = 'image';
    case Video = 'video';
    case List = 'list';
    case Timeline = 'timeline';
    case Gallery = 'gallery';
}
