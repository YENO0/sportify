@php
    $paramPrefix = $param_prefix ?? '';
    $sortParam = $paramPrefix ? $paramPrefix . 'sort' : 'sort';
    $directionParam = $paramPrefix ? $paramPrefix . 'direction' : 'direction';
    
    $currentSort = request($sortParam);
    $currentDirection = request($directionParam);
    $isActive = $currentSort === $column;
    $newDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';
    
    // Preserve tab parameter and other relevant params
    $params = request()->except([$sortParam, $directionParam]);
    $params[$sortParam] = $column;
    $params[$directionParam] = $newDirection;
    
    // Always preserve tab parameter - determine from param_prefix or existing request
    if ($paramPrefix === 'brand_') {
        $params['tab'] = 'brands';
    } elseif ($paramPrefix === 'sport_type_') {
        $params['tab'] = 'sport-types';
    } elseif (request('tab')) {
        $params['tab'] = request('tab');
    } else {
        // Default to equipment tab if no tab specified
        $params['tab'] = 'equipment';
    }
    
    $url = route($route, $params);
@endphp
<a href="{{ $url }}" class="group inline-flex items-center text-black hover:text-gray-900">
    {{ $label }}
    @if($isActive)
        @if($currentDirection == 'asc')
            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        @else
            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
            </svg>
        @endif
    @else
        <svg class="ml-1 h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
        </svg>
    @endif
</a>

