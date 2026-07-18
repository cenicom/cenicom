<?php

enum ComponentType: string
{
    case INPUT = 'input';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case DATE = 'date';
    case FILE = 'file';
    case COLOR = 'color';
    case RANGE = 'range';
}
