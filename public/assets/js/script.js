// public/assets/js/script.js
document.addEventListener('DOMContentLoaded', function () {
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // Dynamic Juz → Surah → Verse
    const juzSelect = document.getElementById('juz');
    const surahSelect = document.getElementById('surah');
    const verseInput = document.getElementById('verse');

    if (juzSelect && surahSelect) {
        juzSelect.addEventListener('change', loadSurahs);
        surahSelect.addEventListener('change', updateVerseMax);
    }

    function loadSurahs() {
        const juz = juzSelect.value;
        if (!juz) return;

        fetch(`?page=get_surahs&juz=${juz}&_=${Date.now()}`)
            .then(r => r.json())
            .then(data => {
                surahSelect.innerHTML = '<option value="">Select Surah</option>';
                data.forEach(s => {
                    const opt = new Option(`${s.surah_number}. ${s.surah_name_ar} (${s.surah_name_en})`, s.surah_number);
                    surahSelect.add(opt);
                });
            });
    }

    function updateVerseMax() {
        const surah = surahSelect.value;
        if (!surah) return;

        fetch(`?page=get_verse_count&surah=${surah}&_=${Date.now()}`)
            .then(r => r.json())
            .then(data => {
                verseInput.max = data.total_verses;
                verseInput.placeholder = `1 to ${data.total_verses}`;
            });
    }

    // Progress Circle Animation
    document.querySelectorAll('.progress-circle').forEach(circle => {
        const progress = circle.dataset.progress;
        const offset = 377 - (377 * progress / 100);
        circle.querySelector('.progress').style.strokeDashoffset = offset;
    });

    // Dynamic layout sizing: calculate available viewport space and set
    // .main-content min-height so footer sits at the bottom without large empty hero.
    // Initial adjustment and on resize/orientation change
    // Keep script.js focused on UI behaviors unrelated to forcing layout heights.
    // Removed runtime height forcing which previously created a large empty gap
    // below the navbar on some pages. Rely on CSS (flexbox) for stable layout.


    // Dark mode removed as per request

});
