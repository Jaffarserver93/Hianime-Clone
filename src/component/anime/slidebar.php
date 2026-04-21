<div class="deslide-wrap">
    <div class="container">
        <div id="slider" class="slider-container swiper-container">
            <div class="swiper-wrapper" id="spotlight-home-content">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <div class="swiper-slide">
                    <div class="deslide-item">
                        <div class="deslide-cover">
                            <div class="deslide-cover-img skeleton skeleton-card"></div>
                        </div>
                        <div class="deslide-item-content">
                            <div class="desi-sub-text"><span class="skeleton skeleton-text skeleton-number"></span></div>
                            <div class="desi-head-title"><span class="skeleton skeleton-text"></span></div>
                            <div class="sc-detail">
                                <div class="scd-item"><span class="skeleton skeleton-text"></span></div>
                                <div class="scd-item"><span class="skeleton skeleton-text"></span></div>
                                <div class="scd-item m-hide"><span class="skeleton skeleton-text"></span></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="desi-description"><span class="skeleton skeleton-text"></span></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"></div>
            <div class="swiper-navigation">
                <div class="swiper-button swiper-button-next"><i class="fas fa-angle-right"></i></div>
                <div class="swiper-button swiper-button-prev"><i class="fas fa-angle-left"></i></div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
