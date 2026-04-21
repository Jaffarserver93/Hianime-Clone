document.addEventListener('DOMContentLoaded', async function () {
    const trendingContainer = document.getElementById('trending-home-content');
    const spotlightContainer = document.getElementById('spotlight-home-content');
    const top10Today = document.getElementById('top10-today-list');
    const top10Week = document.getElementById('top10-week-list');
    const top10Month = document.getElementById('top10-month-list');

    const failSection = (container) => {
        if (!container) return;
        container.innerHTML = '<p class="skeleton-error">Failed to load anime data.</p>';
    };

    const renderTrending = (items) => {
        if (!trendingContainer) return;
        if (!Array.isArray(items) || items.length === 0) {
            failSection(trendingContainer);
            return;
        }

        trendingContainer.innerHTML = '';
        items.forEach((item, index) => {
            const title = item?.title || 'Untitled';
            const jname = item?.jname || '';
            const id = item?.id || '';
            const poster = item?.poster || '';
            const number = item?.number || (index + 1);

            trendingContainer.insertAdjacentHTML('beforeend', `
                <div class="swiper-slide item-qtip" data-id="${id}" style="width: 209px; margin-right: 15px;">
                    <div class="item">
                        <div class="number">
                            <span>${number}</span>
                            <div class="film-title dynamic-name" data-title="${title}" data-jname="${jname}">${title}</div>
                        </div>
                        <a href="/details/${id}" class="film-poster">
                            <img data-src="${poster}" class="film-poster-img lazyloaded" alt="${title}" src="${poster}">
                        </a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            `);
        });

        const trendingSwiper = document.querySelector('#trending-home .swiper-container')?.swiper;
        if (trendingSwiper) trendingSwiper.update();
    };

    const renderSpotlights = (items) => {
        if (!spotlightContainer) return;
        if (!Array.isArray(items) || items.length === 0) {
            failSection(spotlightContainer);
            return;
        }

        spotlightContainer.innerHTML = '';
        items.forEach((anime, index) => {
            const title = anime?.title || 'Untitled';
            const jname = anime?.jname || '';
            const id = anime?.id || '';
            const poster = anime?.poster || '';
            const description = anime?.description || '';
            const tvInfo = anime?.tvInfo || {};
            const episodeInfo = tvInfo?.episodeInfo || {};

            spotlightContainer.insertAdjacentHTML('beforeend', `
                <div class="swiper-slide">
                    <div class="deslide-item">
                        <div class="deslide-cover">
                            <div class="deslide-cover-img">
                                <img class="film-poster-img lazyload" src="${poster}" alt="${title}" fetchpriority="high" loading="eager">
                            </div>
                        </div>
                        <div class="deslide-item-content">
                            <div class="desi-sub-text">#${index + 1} Spotlight</div>
                            <div class="desi-head-title dynamic-name" data-title="${title}" data-jname="${jname}">${title}</div>
                            <div class="sc-detail">
                                <div class="scd-item"><i class="fas fa-play-circle mr-1"></i>&nbsp;${tvInfo?.showType || 'TV'}</div>
                                <div class="scd-item"><i class="fas fa-clock mr-1"></i>&nbsp;${tvInfo?.duration || 'N/A'}</div>
                                <div class="scd-item m-hide"><i class="fa-solid fa-calendar-days mr-1"></i>&nbsp;${tvInfo?.releaseDate || ''}</div>
                                <div class="scd-item mr-1"><span class="quality">&nbsp;${tvInfo?.quality || 'HD'}</span></div>
                                <div class="scd-item">
                                    <div class="tick">
                                        <div class="tick-item tick-sub"><i class="fas fa-closed-captioning mr-1"></i> ${episodeInfo?.sub || '?'}</div>
                                        ${episodeInfo?.dub ? `<div class="tick-item tick-dub"><i class="fas fa-microphone mr-1"></i> ${episodeInfo.dub}</div>` : ''}
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="desi-description">${description}</div>
                            <div class="desi-buttons">
                                <a href="/watch/${id}?ep=1" class="btn btn-primary btn-radius mr-2"><i class="fas fa-play-circle mr-2"></i>Watch Now</a>
                                <a href="/details/${id}" class="btn btn-secondary btn-radius">Detail<i class="fas fa-angle-right ml-2"></i></a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            `);
        });

        const spotlightSwiper = document.querySelector('#slider')?.swiper;
        if (spotlightSwiper) spotlightSwiper.update();
    };

    const renderTop10 = (listNode, items) => {
        if (!listNode) return;
        if (!Array.isArray(items) || items.length === 0) {
            failSection(listNode);
            return;
        }

        listNode.innerHTML = '';
        items.forEach((anime, idx) => {
            const id = anime?.id || '';
            const title = anime?.title || 'Untitled';
            const jname = anime?.jname || '';
            const poster = anime?.poster || '';
            const number = anime?.number || (idx + 1);
            const tvInfo = anime?.tvInfo || {};

            listNode.insertAdjacentHTML('beforeend', `
                <li class="${number <= 3 ? 'item-top' : ''}">
                    <div class="film-number"><span>${number}</span></div>
                    <div class="film-poster item-qtip" data-id="${id}">
                        <img src="${poster}" data-src="${poster}" class="film-poster-img lazyload" alt="${jname}">
                    </div>
                    <div class="film-detail">
                        <h3 class="film-name">
                            <a href="/details/${id}" title="${jname}" class="dynamic-name" data-title="${title}" data-jname="${jname}">${title}</a>
                        </h3>
                        <div class="fd-infor">
                            <div class="tick">
                                ${tvInfo?.sub ? `<div class="tick-item tick-sub"><i class="fas fa-closed-captioning mr-1"></i>${tvInfo.sub}</div>` : ''}
                                ${tvInfo?.dub ? `<div class="tick-item tick-dub"><i class="fas fa-microphone mr-1"></i>${tvInfo.dub}</div>` : ''}
                                ${tvInfo?.eps ? `<div class="tick-item tick-eps">${tvInfo.eps}</div>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
            `);
        });
    };

    try {
        const response = await fetch('/src/ajax/home-sections.php');
        if (!response.ok) throw new Error('Home sections request failed');

        const json = await response.json();
        if (!json?.success || !json?.results) throw new Error('Invalid payload');

        renderTrending(json.results.trending);
        renderSpotlights(json.results.spotlights);
        renderTop10(top10Today, json.results.top10?.today);
        renderTop10(top10Week, json.results.top10?.week);
        renderTop10(top10Month, json.results.top10?.month);
    } catch (error) {
        console.error('Error loading home sections:', error);
        failSection(trendingContainer);
        failSection(spotlightContainer);
        failSection(top10Today);
        failSection(top10Week);
        failSection(top10Month);
    }
});
