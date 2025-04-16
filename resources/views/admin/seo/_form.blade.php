{{-- resources/views/admin/seo/_form.blade.php --}}

@csrf

<div class="row">
    <div class="col-md-8">
        {{-- General Info --}}
        <div class="card mb-4">
            <div class="card-header">General SEO Information</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="name">Admin Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $seo->name ?? '') }}" required placeholder="e.g., Homepage SEO, About Us Page">
                     <small class="form-text text-muted">Internal name for identifying this SEO setting.</small>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="key">Page Key <span class="text-danger">*</span></label>
                    <input type="text" id="key" name="key" class="form-control @error('key') is-invalid @enderror"
                           value="{{ old('key', $seo->key ?? '') }}" required placeholder="e.g., home, about.us, contact-page">
                     <small class="form-text text-muted">Unique identifier for the page (lowercase, numbers, ., _, -).</small>
                    @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="title">Meta Title <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $seo->title ?? '') }}" required maxlength="70">
                     <small class="form-text text-muted">Max 70 characters recommended.</small>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 <div class="form-group mb-3">
                    <label for="description">Meta Description <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3" required maxlength="160">{{ old('description', $seo->description ?? '') }}</textarea>
                      <small class="form-text text-muted">Max 160 characters recommended.</small>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 <div class="form-group mb-3">
                    <label for="keywords">Keywords</label>
                    <input type="text" id="keywords" name="keywords" class="form-control @error('keywords') is-invalid @enderror"
                           value="{{ old('keywords', $seo->keywords ?? '') }}">
                    <small class="form-text text-muted">Comma-separated keywords (less important nowadays).</small>
                    @error('keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

         {{-- Open Graph (Facebook, LinkedIn, etc.) --}}
        <div class="card mb-4">
            <div class="card-header">Open Graph Tags (Social Sharing)</div>
            <div class="card-body">
                 <div class="form-group mb-3">
                    <label for="og_title">OG Title</label>
                    <input type="text" id="og_title" name="og_title" class="form-control @error('og_title') is-invalid @enderror"
                           value="{{ old('og_title', $seo->og_title ?? '') }}" placeholder="Defaults to Meta Title if empty">
                    @error('og_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group mb-3">
                    <label for="og_description">OG Description</label>
                    <textarea id="og_description" name="og_description" class="form-control @error('og_description') is-invalid @enderror"
                              rows="2" placeholder="Defaults to Meta Description if empty">{{ old('og_description', $seo->og_description ?? '') }}</textarea>
                    @error('og_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group mb-3">
                    <label for="og_image">OG Image</label>
                    <input type="file" id="og_image" name="og_image" class="form-control @error('og_image') is-invalid @enderror" accept="image/*">
                     <small class="form-text text-muted">Recommended size: 1200x630 pixels. Max 1MB.</small>
                    @error('og_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                     @if(isset($seo) && $seo->og_image_url)
                        <div class="mt-2">
                             <p class="mb-1"><small>Current OG Image:</small></p>
                            <img src="{{ $seo->og_image_url }}" alt="OG Image" style="max-height: 100px;" class="img-thumbnail">
                             <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="remove_og_image" id="remove_og_image" value="1">
                                <label class="form-check-label text-danger" for="remove_og_image" style="font-size: 0.8em;">Remove</label>
                            </div>
                        </div>
                    @endif
                </div>
                 <div class="form-group mb-3">
                    <label for="og_image_alt">OG Image Alt Text</label>
                    <input type="text" id="og_image_alt" name="og_image_alt" class="form-control @error('og_image_alt') is-invalid @enderror"
                           value="{{ old('og_image_alt', $seo->og_image_alt ?? '') }}">
                    @error('og_image_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="og_locale">OG Locale</label>
                        <input type="text" id="og_locale" name="og_locale" class="form-control @error('og_locale') is-invalid @enderror"
                               value="{{ old('og_locale', $seo->og_locale ?? 'en_US') }}" placeholder="e.g., en_US, ar_AE">
                        @error('og_locale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="og_site_name">OG Site Name</label>
                        <input type="text" id="og_site_name" name="og_site_name" class="form-control @error('og_site_name') is-invalid @enderror"
                               value="{{ old('og_site_name', $seo->og_site_name ?? config('app.name')) }}"> {{-- Default to app name --}}
                        @error('og_site_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                 </div>
            </div>
        </div>

         {{-- Twitter Card Tags --}}
        <div class="card mb-4">
             <div class="card-header">Twitter Card Tags</div>
             <div class="card-body">
                  <div class="form-group mb-3">
                    <label for="twitter_title">Twitter Title</label>
                    <input type="text" id="twitter_title" name="twitter_title" class="form-control @error('twitter_title') is-invalid @enderror"
                           value="{{ old('twitter_title', $seo->twitter_title ?? '') }}" placeholder="Defaults to Meta Title if empty">
                    @error('twitter_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group mb-3">
                    <label for="twitter_description">Twitter Description</label>
                    <textarea id="twitter_description" name="twitter_description" class="form-control @error('twitter_description') is-invalid @enderror"
                              rows="2" placeholder="Defaults to Meta Description if empty">{{ old('twitter_description', $seo->twitter_description ?? '') }}</textarea>
                    @error('twitter_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="twitter_image">Twitter Image</label>
                    <input type="file" id="twitter_image" name="twitter_image" class="form-control @error('twitter_image') is-invalid @enderror" accept="image/*">
                    <small class="form-text text-muted">Min size 144x144, max 4096x4096, <1MB. Uses OG image if empty.</small>
                    @error('twitter_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($seo) && $seo->twitter_image_url)
                        <div class="mt-2">
                             <p class="mb-1"><small>Current Twitter Image:</small></p>
                            <img src="{{ $seo->twitter_image_url }}" alt="Twitter Image" style="max-height: 100px;" class="img-thumbnail">
                             <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="remove_twitter_image" id="remove_twitter_image" value="1">
                                <label class="form-check-label text-danger" for="remove_twitter_image" style="font-size: 0.8em;">Remove</label>
                            </div>
                        </div>
                    @endif
                </div>
                 <div class="form-group mb-3">
                    <label for="twitter_alt">Twitter Image Alt Text</label>
                    <input type="text" id="twitter_alt" name="twitter_alt" class="form-control @error('twitter_alt') is-invalid @enderror"
                           value="{{ old('twitter_alt', $seo->twitter_alt ?? '') }}">
                     @error('twitter_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
             </div>
        </div>


    </div> {{-- End Left Column --}}

    <div class="col-md-4">
        {{-- Advanced Settings --}}
        <div class="card mb-4">
            <div class="card-header">Advanced Settings</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="robots_meta">Robots Meta Tag</label>
                    <input type="text" id="robots_meta" name="robots_meta" class="form-control @error('robots_meta') is-invalid @enderror"
                           value="{{ old('robots_meta', $seo->robots_meta ?? 'index, follow') }}" placeholder="e.g., index, follow">
                     <small class="form-text text-muted">Controls search engine crawling and indexing.</small>
                    @error('robots_meta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="canonical_url">Canonical URL</label>
                    <input type="url" id="canonical_url" name="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror"
                           value="{{ old('canonical_url', $seo->canonical_url ?? '') }}" placeholder="Leave empty for self-referencing">
                    <small class="form-text text-muted">Preferred URL for this page if content is duplicated.</small>
                     @error('canonical_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 <div class="form-group mb-3">
                    <label for="custom_meta_tags">Custom Meta Tags</label>
                    <textarea id="custom_meta_tags" name="custom_meta_tags" class="form-control @error('custom_meta_tags') is-invalid @enderror"
                              rows="4" placeholder="e.g., <meta name='custom' content='value'>">{{ old('custom_meta_tags', $seo->custom_meta_tags ?? '') }}</textarea>
                     <small class="form-text text-muted">Add any additional meta tags here (use with caution).</small>
                    @error('custom_meta_tags') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div> {{-- End Right Column --}}
</div> {{-- End Row --}}

<div class="form-actions mt-0 pt-3 border-top"> {{-- Removed mt-4 --}}
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($seo) ? 'Update SEO Settings' : 'Create SEO Settings' }}
    </button>
    <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary">Cancel</a>
</div>