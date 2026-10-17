@extends('layouts.app')

@section('main')

<style>
/* ==========================================================================
   Pal Varnan — Premium Categories & Subcategories Showcase
   Crafted with UI UX Pro Max · Modern E-Commerce Standard
   ========================================================================== */

:root {
    --cat-radius: 16px;
    --cat-radius-sm: 10px;
    --cat-border: #e2e8f0;
    --cat-border-hover: var(--main-600, #026966);
    --cat-bg-subtle: #f8fafc;
    --cat-ink-primary: #0f172a;
    --cat-ink-secondary: #475569;
    --cat-ink-muted: #94a3b8;
    --cat-shadow-card: 0 1px 3px rgba(15, 23, 42, 0.05), 0 10px 24px -8px rgba(15, 23, 42, 0.04);
    --cat-shadow-hover: 0 16px 32px -8px hsl(var(--main, 178 96% 21%) / 0.16), 0 4px 12px -2px rgba(15, 23, 42, 0.06);
}

/* Breadcrumb */
.cat-breadcrumb {
    background: #ffffff;
    border-bottom: 1px solid var(--cat-border);
    padding: 14px 0;
}

/* Hero Section */
.cat-hero {
    background: linear-gradient(180deg, #ffffff 0%, var(--main-50, #f0fdfa) 100%);
    border-bottom: 1px solid var(--cat-border);
    padding: 36px 0 28px;
}

.cat-hero__title {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--cat-ink-primary);
    letter-spacing: -0.02em;
    margin-bottom: 6px;
}

.cat-hero__subtitle {
    font-size: 14.5px;
    color: var(--cat-ink-secondary);
    margin-bottom: 22px;
}

/* Search Bar */
.cat-search-wrapper {
    position: relative;
    max-width: 480px;
    margin: 0 auto;
}

.cat-search-input {
    width: 100%;
    height: 48px;
    border: 1.5px solid var(--cat-border);
    border-radius: 50rem;
    padding: 0 42px 0 44px;
    font-size: 14px;
    color: var(--cat-ink-primary);
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.cat-search-input:focus {
    outline: none;
    border-color: var(--main-600, #026966);
    box-shadow: 0 0 0 4px hsl(var(--main, 178 96% 21%) / 0.12);
}

.cat-search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--cat-ink-muted);
    font-size: 18px;
    pointer-events: none;
}

.cat-search-clear {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: #f1f5f9;
    color: var(--cat-ink-secondary);
    border: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: background 0.15s ease;
}

.cat-search-clear:hover {
    background: #e2e8f0;
    color: var(--cat-ink-primary);
}

/* Category Card */
.cat-card {
    background: #ffffff;
    border: 1px solid var(--cat-border);
    border-radius: var(--cat-radius);
    padding: 20px;
    box-shadow: var(--cat-shadow-card);
    transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
}

.cat-card:hover {
    transform: translateY(-3px);
    border-color: var(--cat-border-hover);
    box-shadow: var(--cat-shadow-hover);
}

/* Card Header */
.cat-card__header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
}

.cat-card__thumb-wrap {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    background: var(--main-50, #f0fdfa);
    border: 1px solid rgba(2, 105, 102, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
    transition: transform 0.2s ease;
}

.cat-card:hover .cat-card__thumb-wrap {
    transform: scale(1.05);
}

.cat-card__thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cat-card__title {
    font-size: 16.5px;
    font-weight: 700;
    color: var(--cat-ink-primary);
    margin-bottom: 4px;
    line-height: 1.3;
}

.cat-card__title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.15s ease;
}

.cat-card__title a:hover {
    color: var(--main-600, #026966);
}

.cat-card__badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f1f5f9;
    color: var(--cat-ink-secondary);
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
}

/* Subcategories Section */
.cat-card__body {
    padding: 14px 0;
    flex-grow: 1;
}

.cat-card__sub-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--cat-ink-muted);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.cat-card__sub-count {
    background: #f1f5f9;
    color: var(--cat-ink-secondary);
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 9999px;
}

.cat-pills-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.cat-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--cat-bg-subtle);
    color: var(--cat-ink-secondary);
    border: 1px solid var(--cat-border);
    padding: 5px 11px;
    border-radius: 50rem;
    font-size: 12.5px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.16s ease;
}

.cat-pill:hover {
    background: var(--main-600, #026966);
    color: #ffffff;
    border-color: var(--main-600, #026966);
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(2, 105, 102, 0.22);
}

.cat-pill i {
    font-size: 10px;
    color: var(--cat-ink-muted);
    transition: color 0.16s ease;
}

.cat-pill:hover i {
    color: #ffffff;
}

.cat-direct-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--main-600, #026966);
    font-weight: 600;
    text-decoration: none;
    padding: 6px 12px;
    background: var(--main-50, #f0fdfa);
    border-radius: 8px;
    border: 1px dashed rgba(2, 105, 102, 0.3);
    transition: all 0.16s ease;
}

.cat-direct-link:hover {
    background: var(--main-600, #026966);
    color: #ffffff;
    border-style: solid;
}

/* Card Action */
.cat-card__footer {
    padding-top: 14px;
    border-top: 1px solid #f8fafc;
}

.cat-explore-btn {
    width: 100%;
    height: 38px;
    background: #f8fafc;
    color: var(--cat-ink-primary);
    border: 1px solid var(--cat-border);
    border-radius: 50rem;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.18s ease;
}

.cat-explore-btn:hover {
    background: var(--main-600, #026966);
    color: #ffffff;
    border-color: var(--main-600, #026966);
    box-shadow: 0 4px 12px rgba(2, 105, 102, 0.18);
}

.cat-explore-btn i {
    font-size: 13px;
    transition: transform 0.18s ease;
}

.cat-explore-btn:hover i {
    transform: translateX(3px);
}

/* Empty State */
.cat-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #ffffff;
    border: 1px dashed var(--cat-border);
    border-radius: var(--cat-radius);
    max-width: 520px;
    margin: 0 auto;
}
</style>

<!-- ========================= Breadcrumb =============================== -->
<div class="cat-breadcrumb">
    <div class="container container-lg">
        <ul class="flex-align gap-8 flex-wrap list-unstyled mb-0 text-xs text-neutral-500">
            <li>
                <a href="{{ url('/') }}" class="text-neutral-600 flex-align gap-6 hover-text-main-600 text-decoration-none">
                    <i class="ph ph-house text-sm"></i>
                    Home
                </a>
            </li>
            <li class="flex-align text-neutral-300">
                <i class="ph ph-caret-right"></i>
            </li>
            <li class="text-main-600 fw-semibold"> Categories </li>
        </ul>
    </div>
</div>

<!-- ========================= Hero & Instant Search =============================== -->
<section class="cat-hero">
    <div class="container container-lg text-center">
        <h1 class="cat-hero__title">Categories</h1>
        <p class="cat-hero__subtitle">Find products by category or subcategory</p>

        <!-- Search Bar -->
        <div class="cat-search-wrapper">
            <i class="ph-bold ph-magnifying-glass cat-search-icon"></i>
            <input type="text" id="catSearchInput" class="cat-search-input" placeholder="Search categories..." onkeyup="filterCategories()">
            <button type="button" id="catClearBtn" class="cat-search-clear" onclick="clearCatSearch()" aria-label="Clear search">
                <i class="ph-bold ph-x"></i>
            </button>
        </div>

        <div class="d-flex justify-content-center align-items-center gap-16 mt-16 text-xs text-neutral-500">
            <span><strong>{{ $categories->count() }}</strong> Categories</span>
            <span class="text-neutral-300">•</span>
            <span><strong>{{ $subcategories->count() }}</strong> Subcategories</span>
        </div>
    </div>
</section>

<!-- ========================= Categories Grid =============================== -->
<section class="py-48 bg-white">
    <div class="container container-lg">

        <div class="row gy-4 gx-3 gx-lg-4" id="categoriesGrid">
            @forelse($categories as $category)
                <div class="col-xl-3 col-lg-4 col-sm-6 col-12 cat-item-col" data-cat-name="{{ strtolower($category->category_name) }}">
                    <div class="cat-card">
                        
                        <!-- Header -->
                        <div class="cat-card__header">
                            <div class="cat-card__thumb-wrap">
                                @if($category->category_image)
                                    <img src="{{ env('MAIN_URL') }}images/{{ $category->category_image }}" 
                                         alt="{{ $category->category_name }}" 
                                         class="cat-card__thumb"
                                         onerror="this.src='/assets/images/thumbs/category-img1.png'">
                                @else
                                    <i class="ph-bold ph-squares-four text-main-600 text-2xl"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="cat-card__title text-truncate">
                                    <a href="{{ url('/products?category=' . \Illuminate\Support\Str::slug($category->category_name)) }}">
                                        {{ $category->category_name }}
                                    </a>
                                </h3>
                                <span class="cat-card__badge">
                                    {{ $category->products_count ?? 0 }} {{ ($category->products_count ?? 0) === 1 ? 'item' : 'items' }}
                                </span>
                            </div>
                        </div>

                        <!-- Subcategories Section -->
                        <div class="cat-card__body">
                            <div class="cat-card__sub-label">
                                <span>Subcategories</span>
                                @if($category->subs->count() > 0)
                                    <span class="cat-card__sub-count">{{ $category->subs->count() }}</span>
                                @endif
                            </div>

                            @if($category->subs->count() > 0)
                                <div class="cat-pills-grid">
                                    @foreach($category->subs as $sub)
                                        <a href="{{ url('/products?category=' . \Illuminate\Support\Str::slug($category->category_name) . '&subcategory=' . \Illuminate\Support\Str::slug($sub->subcategory_name)) }}" 
                                           class="cat-pill" 
                                           data-sub-name="{{ strtolower($sub->subcategory_name) }}">
                                            <span>{{ $sub->subcategory_name }}</span>
                                            <i class="ph-bold ph-caret-right"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <a href="{{ url('/products?category=' . \Illuminate\Support\Str::slug($category->category_name)) }}" class="cat-direct-link">
                                    <span>Browse {{ $category->category_name }}</span>
                                    <i class="ph-bold ph-arrow-right text-xs"></i>
                                </a>
                            @endif
                        </div>

                        <!-- Action Button -->
                        <div class="cat-card__footer">
                            <a href="{{ url('/products?category=' . \Illuminate\Support\Str::slug($category->category_name)) }}" class="cat-explore-btn">
                                <span>View Products</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="cat-empty-state">
                        <i class="ph-bold ph-package text-main-600 text-3xl mb-12 d-block"></i>
                        <h5 class="fw-bold text-heading mb-6">No Categories Found</h5>
                        <p class="text-neutral-500 text-xs mb-16">No categories are currently available for this delivery region.</p>
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#countryModal" class="btn btn-sm btn-main rounded-pill px-20">
                            Change Country
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- No search results fallback -->
        <div id="noMatchState" class="cat-empty-state d-none my-40">
            <i class="ph-bold ph-magnifying-glass text-neutral-400 text-3xl mb-12 d-block"></i>
            <h6 class="fw-bold text-heading mb-6">No matching categories found</h6>
            <p class="text-neutral-500 text-xs mb-16">Try searching with a different keyword.</p>
            <button type="button" class="btn btn-sm btn-outline-main rounded-pill px-20" onclick="clearCatSearch()">
                Clear Search
            </button>
        </div>

    </div>
</section>

<script>
function filterCategories() {
    const input = document.getElementById('catSearchInput').value.toLowerCase().trim();
    const clearBtn = document.getElementById('catClearBtn');
    const cols = document.querySelectorAll('.cat-item-col');
    let visibleCount = 0;

    clearBtn.style.display = input.length > 0 ? 'inline-flex' : 'none';

    cols.forEach(col => {
        const catName = col.getAttribute('data-cat-name') || '';
        const subPills = col.querySelectorAll('.cat-pill');
        let subMatch = false;

        subPills.forEach(pill => {
            const subName = pill.getAttribute('data-sub-name') || '';
            if (input === '' || subName.includes(input)) {
                pill.style.display = 'inline-flex';
                if (input !== '' && subName.includes(input)) {
                    subMatch = true;
                }
            } else {
                pill.style.display = 'none';
            }
        });

        if (input === '' || catName.includes(input) || subMatch) {
            col.style.display = 'block';
            visibleCount++;
        } else {
            col.style.display = 'none';
        }
    });

    const noMatch = document.getElementById('noMatchState');
    if (visibleCount === 0 && input !== '') {
        noMatch.classList.remove('d-none');
    } else {
        noMatch.classList.add('d-none');
    }
}

function clearCatSearch() {
    const input = document.getElementById('catSearchInput');
    input.value = '';
    filterCategories();
    input.focus();
}
</script>

@endsection
