<div id="anime-trending">
        <div class="container">
            <section class="block_area block_area_trending">
                <div class="block_area-header">
                    <div class="bah-heading">
                        <h2 class="cat-heading">Trending</h2>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="block_area-content">
                    <div class="trending-list" id="trending-home">
                        <div class="swiper-container swiper-container-initialized swiper-container-horizontal">
                            <div class="swiper-wrapper" id="trending-home-content" style="transition-duration: 0ms; transform: translate3d(0px, 0px, 0px);">
                                <?php for ($i = 0; $i < 8; $i++): ?>
                                <div class="swiper-slide" style="width: 209px; margin-right: 15px;">
                                    <div class="item">
                                        <div class="number"><span class="skeleton skeleton-text skeleton-number"></span></div>
                                        <div class="film-title"><span class="skeleton skeleton-text"></span></div>
                                        <div class="film-poster skeleton skeleton-card"></div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        <div class="clearfix"></div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                        <div class="trending-navi">
                            <div class="navi-next" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false"><i class="fas fa-angle-right"></i></div>
                            <div class="navi-prev swiper-button-disabled" tabindex="-1" role="button" aria-label="Previous slide" aria-disabled="true"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
