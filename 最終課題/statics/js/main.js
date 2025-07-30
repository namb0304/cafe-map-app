// --- 最終課題/statics/js/main.js (最終修正版) ---

// この関数は、Google Maps APIの読み込みが完了したときに自動的に呼ばれます。
function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    const map = new google.maps.Map(mapElement, {
        center: { lat: 35.6812, lng: 139.7671 },
        zoom: 13,
        mapTypeControl: false,
        streetViewControl: false
    });

    const searchBtn = document.getElementById('search-this-area-btn');
    const cafeListContent = document.getElementById('cafe-list-content');
    let markers = [];
    let currentInfoWindow = null; // 現在開いている情報ウィンドウを管理

    const fetchAndDisplayCafes = async () => {
        const bounds = map.getBounds();
        if (!bounds) return;
        const ne = bounds.getNorthEast();
        const sw = bounds.getSouthWest();

        searchBtn.textContent = '検索中...';
        searchBtn.disabled = true;

        try {
            const response = await fetch(`api/get_cafes.php?sw_lat=${sw.lat()}&sw_lng=${sw.lng()}&ne_lat=${ne.lat()}&ne_lng=${ne.lng()}`);
            if (!response.ok) throw new Error('サーバーからの応答がありません。');
            
            const cafes = await response.json();
            updateMapAndList(cafes);

        } catch (error) {
            console.error('カフェ情報の取得に失敗しました:', error);
            cafeListContent.innerHTML = '<p>情報の取得に失敗しました。再度お試しください。</p>';
        } finally {
            searchBtn.textContent = 'このエリアで再検索';
            searchBtn.disabled = false;
            searchBtn.style.display = 'none';
        }
    };

    const updateMapAndList = (cafes) => {
        markers.forEach(marker => marker.setMap(null));
        markers = [];
        cafeListContent.innerHTML = '';

        if (cafes.length === 0) {
            cafeListContent.innerHTML = '<p>このエリアにはカフェが見つかりませんでした。</p>';
            return;
        }

        cafes.forEach(cafe => {
            if (cafe.latitude && cafe.longitude) {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(cafe.latitude), lng: parseFloat(cafe.longitude) },
                    map: map,
                    title: cafe.name
                });

                let popupContent = `<div class="map-popup"><b>${cafe.name}</b><br><a href="cafe_details.php?id=${cafe.id}">レビューを見る・投稿する</a>`;
                if (cafe.url) {
                    popupContent += `<br><a href="${cafe.url}" target="_blank">公式サイト</a>`;
                }
                popupContent += `</div>`;
                
                const infowindow = new google.maps.InfoWindow({ content: popupContent });

                marker.addListener('click', () => {
                    if (currentInfoWindow) {
                        currentInfoWindow.close();
                    }
                    infowindow.open(map, marker);
                    currentInfoWindow = infowindow;
                });
                markers.push(marker);
            }
            const card = document.createElement('div');
            card.className = 'cafe-card';
            card.innerHTML = `<h2><a href="cafe_details.php?id=${cafe.id}">${cafe.name}</a></h2><p class="address">${cafe.address}</p>`;
            cafeListContent.appendChild(card);
        });
    };

    map.addListener('idle', () => { searchBtn.style.display = 'block'; });
    searchBtn.addEventListener('click', fetchAndDisplayCafes);

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const pos = { lat: position.coords.latitude, lng: position.coords.longitude };
            map.setCenter(pos);
            map.setZoom(15);
            new google.maps.Marker({
                position: pos,
                map: map,
                title: "あなたの現在地",
                // ▼▼▼ ここの記述ミスを修正しました ▼▼▼
                icon: '[http://maps.google.com/mapfiles/ms/icons/blue-dot.png](http://maps.google.com/mapfiles/ms/icons/blue-dot.png)'
            });
            fetchAndDisplayCafes();
        },
        () => {
            console.warn("現在地の取得に失敗しました。");
            fetchAndDisplayCafes();
        }
    );
}