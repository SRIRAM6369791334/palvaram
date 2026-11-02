<?php
$file = __DIR__ . '/../palVarnan/resources/views/pages/wishlist.blade.php';
$content = file_get_contents($file);

$oldBadge = <<<'HTML'
                                                    @if($variant && !empty($variant->value) && $variant->unit)
                                                        <span class="text-xs fw-semibold text-main-600 bg-main-50 border border-main-200 rounded-pill px-10 py-2 d-inline-block mt-4">
                                                            {{ $variant->value }}{{ $variant->unit->short_name ?? '' }}
                                                        </span>
                                                    @endif
HTML;

$newBadge = <<<'HTML'
                                                    @if($variant && !empty($variant->value) && $variant->unit)
                                                        <span class="text-xs fw-semibold text-main-600 bg-main-50 border border-main-200 rounded-pill px-10 py-2 d-inline-block mt-4">
                                                            {{ $variant->value }}{{ $variant->unit->short_name ?? '' }}
                                                        </span>
                                                    @elseif($variant && ($variant->size || $variant->color || $variant->varient_name))
                                                        @php
                                                            $giftParts = [];
                                                            if ($variant->size) $giftParts[] = 'Size: ' . $variant->size->size;
                                                            if ($variant->color) $giftParts[] = 'Color: ' . $variant->color->color;
                                                            $giftLabel = !empty($giftParts) ? implode(' | ', $giftParts) : ($variant->varient_name ?: 'Gift Item');
                                                        @endphp
                                                        <span class="text-xs fw-semibold text-purple-700 bg-purple-50 border border-purple-200 rounded-pill px-10 py-2 d-inline-block mt-4">
                                                            <i class="ph-bold ph-gift me-1"></i>{{ $giftLabel }}
                                                        </span>
                                                    @endif
HTML;

if (strpos($content, $oldBadge) !== false) {
    $content = str_replace($oldBadge, $newBadge, $content);
} else {
    // Regex replace
    $pattern = '/@if\(\$variant && !empty\(\$variant->value\) && \$variant->unit\).*?@endif/s';
    $content = preg_replace($pattern, $newBadge, $content);
}

// Also update showLoginAlert function in wishlist.blade.php
$loginAlertOld = 'function showLoginAlert()';
$loginAlertNew = <<<'HTML'
function showLoginAlert() {
    Swal.fire({
        icon: 'info',
        title: 'Login Required',
        text: 'Please login to manage items in your wishlist or add them to cart.',
        confirmButtonColor: '#0c716a',
        confirmButtonText: 'Login Now',
        showCancelButton: true,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "/login?redirect=/wishlist";
        }
    });
}
HTML;

if (strpos($content, 'function showLoginAlert') !== false) {
    $pattern = '/function showLoginAlert\(\)\s*\{.*?\}/s';
    $content = preg_replace($pattern, $loginAlertNew, $content);
}

file_put_contents($file, $content);
echo "wishlist.blade.php updated successfully!\n";
