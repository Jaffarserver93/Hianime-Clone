<div id="schedule-block"><section class="block_area block_area_sidebar block_area-schedule schedule-full">
    <div class="block_area-header">
        <div class="float-left bah-heading mr-4">
            <h2 class="cat-heading">Estimated Schedule</h2>
        </div>
        <div class="float-left bah-time">
            <span class="current-time">
                <span id="timezone"></span> 
                <span id="current-date"></span> 
                <span id="clock"></span>
            </span>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="block_area-content">
        <div class="table_schedule">
            <div class="table_schedule-date">
                <div class="swiper-container swiper-container-initialized swiper-container-horizontal">
                    <div class="swiper-wrapper" id="schedule-dates">
                        <!-- Date slides will be populated dynamically -->
                    </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                <div class="ts-navigation">
                    <button class="btn tsn-next" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false"><i class="fas fa-angle-right"></i></button>
                    <button class="btn tsn-prev" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="false"><i class="fas fa-angle-left"></i></button>
                </div>
            </div>
            <div class="clearfix"></div>
            <ul class="ulclear table_schedule-list limit-8" id="schedule-items">
                <?php for ($i = 0; $i < 8; $i++): ?>
                    <li>
                        <div class="tsl-link">
                            <div class="time skeleton skeleton-text skeleton-time"></div>
                            <div class="film-detail">
                                <h3 class="film-name"><span class="skeleton skeleton-text"></span></h3>
                                <div class="fd-play">
                                    <span class="skeleton skeleton-text skeleton-episode"></span>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endfor; ?>
            </ul>
            <button id="scl-more" class="btn btn-sm btn-block btn-showmore" style="display: none;"></button>
        </div>
    </div>
</section>
</div>
<script>
const JIKAN_API_BASE_URL = 'https://api.jikan.moe/v4';

function formatDateYYYYMMDD(dateInput) {
    const date = dateInput instanceof Date ? dateInput : new Date(dateInput);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function generateDates() {
    const dates = [];
    const today = new Date();
    
    for(let i = 0; i < 31; i++) {
        const date = new Date();
        date.setDate(today.getDate() + i);
        
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        dates.push({
            dayName: dayNames[date.getDay()],
            monthName: monthNames[date.getMonth()],
            date: date.getDate(),
            fullDate: formatDateYYYYMMDD(date)
        });
    }
    return dates;
}


function renderDates(dates) {
    const container = document.getElementById('schedule-dates');
    const today = formatDateYYYYMMDD(new Date());
    
    container.innerHTML = '';
    dates.forEach(date => {
        const isActive = date.fullDate === today;
        container.innerHTML += `
            <div class="swiper-slide day-item" data-date="${date.fullDate}" style="width: 163.8px; margin-right: 10px;">
                <div class="tsd-item ${isActive ? 'active' : ''}">
                    <span>${date.dayName}</span>
                    <div class="date">${date.monthName} ${date.date}</div>
                </div>
            </div>
        `;
    });
}

async function fetchSchedule(date) {
    try {
        const formattedDate = /^\d{4}-\d{2}-\d{2}$/.test(date) ? date : formatDateYYYYMMDD(date);
        const dateObj = new Date(`${formattedDate}T00:00:00`);
        const weekday = dateObj.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
        const response = await fetch(`${JIKAN_API_BASE_URL}/schedules?filter=${weekday}`);
        const data = await response.json();
        const scheduleItems = Array.isArray(data?.data) ? data.data : [];
        
        const container = document.getElementById('schedule-items');
        container.innerHTML = '';

        if (scheduleItems.length === 0) {
            container.innerHTML = `<li class="text-center py-3">No schedule available for this date</li>`;
        } else {
            scheduleItems?.forEach(item => {
                const displayTitle = item.title_english ? item.title_english : item.title;
                container.innerHTML += `
                    <li>
                        <a href="/details/${item.mal_id}" class="tsl-link">
                            <div class="time">${item.broadcast?.time ?? 'TBA'}</div>
                            <div class="film-detail">
                                <h3 class="film-name dynamic-name" data-jname="${item.title_japanese ?? ''}">${displayTitle ?? 'Untitled'}</h3>
                                <div class="fd-play">
                                    <button type="button" class="btn btn-sm btn-play">
                                        <i class="fas fa-play mr-2"></i>Episode ${item.episodes ?? '?'}
                                    </button>
                                </div>
                            </div>
                        </a>
                    </li>
                `;
            });
        }

        if (scheduleItems.length > 7) {
            document.getElementById('scl-more').style.display = 'block';
        } else {
            document.getElementById('scl-more').style.display = 'none';
        }
    } catch (error) {
        console.error('Error fetching schedule:', error);
        const container = document.getElementById('schedule-items');
        container.innerHTML = '<li class="text-center py-3">Failed to load anime data.</li>';
        document.getElementById('scl-more').style.display = 'none';
    }
}

function updateTime() {
    const now = new Date();
    
    // Format the time in locale-specific format
    const currentTime = now.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    
    // Date and Timezone
    const timezoneOffset = -now.getTimezoneOffset() / 60;
    const timezone = `GMT${timezoneOffset >= 0 ? '+' : ''}${timezoneOffset}:00`;
    
    // Update DOM
    document.getElementById('clock').textContent = currentTime;
    document.getElementById('current-date').textContent = now.toLocaleDateString();
    document.getElementById('timezone').textContent = `(${timezone})`;
}

document.addEventListener('DOMContentLoaded', async function() {
    const dates = generateDates();
    renderDates(dates);
    
    // Initialize time updates
    setInterval(updateTime, 1000);
    updateTime();
    
    // Initialize Swiper after dates are rendered
    const scheduleSw = new Swiper('.schedule-full .table_schedule-date .swiper-container', {
        slidesPerView: 7,
        spaceBetween: 10,
        navigation: {
            nextEl: '.schedule-full .tsn-next',
            prevEl: '.schedule-full .tsn-prev',
        },
        breakpoints: {
            320: { slidesPerView: 3, spaceBetween: 10 },
            360: { slidesPerView: 3, spaceBetween: 10 },
            480: { slidesPerView: 3, spaceBetween: 10 },
            640: { slidesPerView: 4, spaceBetween: 10 },
            768: { slidesPerView: 5, spaceBetween: 10 },
            1024: { slidesPerView: 7, spaceBetween: 13 },
        },
    });

    // Schedule more button
    document.getElementById('scl-more')?.addEventListener('click', function() {
        this.parentElement.querySelector('.limit-8')?.classList.toggle('active');
        this.classList.toggle('active');
    });
    
    // Date item clicks
    document.querySelectorAll('.day-item').forEach(item => {
        item.addEventListener('click', async function() {
            document.querySelectorAll('.tsd-item').forEach(el => el.classList.remove('active'));
            this.querySelector('.tsd-item').classList.add('active');
            await fetchSchedule(this.dataset.date);
        });
    });
    
    // Initial schedule fetch
    fetchSchedule(formatDateYYYYMMDD(new Date()));
    
    // Slide to active item
    const activeIndex = Array.from(document.querySelectorAll('.tsd-item'))
        .findIndex(item => item.classList.contains('active'));
    if (activeIndex !== -1) {
        scheduleSw.slideTo(activeIndex, 1000);
    }
});
</script>
