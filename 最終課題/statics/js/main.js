// --- 最終課題/statics/js/main.js ---

// このinitMap関数は、Google Maps APIのスクリプトから自動的に呼び出されます
function initMap() {
    // --- 変数定義 ---
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    // 東京駅を中心に地図を初期化
    const map = new google.maps.Map(mapElement, {
        center: { lat: 35.6812, lng: 139.7671 },
        zoom: 13,
        mapTypeControl: false, // 右上の「地図」「航空写真」ボタンを非表示
        streetViewControl: false // ストリートビューの人形アイコンを非表示
    });

    const searchBtn = document.getElementById('search-this-area-btn');
    const cafeListContent = document.getElementById('cafe-list-content');
    let markers = []; // マーカーを管理するための配列

    // --- 関数定義 ---

    // カフェデータを取得してマップとリストを更新する関数
    const fetchAndDisplayCafes = async () => {
        const bounds = map.getBounds();
        const ne = bounds.getNorthEast(); // 北東の座標
        const sw = bounds.getSouthWest(); // 南西の座標

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

    // マップとリストの表示を更新する関数
    const updateMapAndList = (cafes) => {
        // 既存のマーカーを全て削除
        markers.forEach(marker => marker.setMap(null));
        markers = [];
        cafeListContent.innerHTML = '';

        if (cafes.length === 0) {
            cafeListContent.innerHTML = '<p>このエリアにはカフェが見つかりませんでした。</p>';
            return;
        }

        cafes.forEach(cafe => {
            if (cafe.latitude && cafe.longitude) {
                // マーカーを作成
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(cafe.latitude), lng: parseFloat(cafe.longitude) },
                    map: map,
                    title: cafe.name
                });

                // 情報ウィンドウ（ポップアップ）を作成
                const infowindow = new google.maps.InfoWindow({
                    content: `<b>${cafe.name}</b><br>${cafe.address}`
                });

                // マーカーがクリックされたら情報ウィンドウを開く
                marker.addListener('click', () => {
                    infowindow.open(map, marker);
                });

                markers.push(marker);
            }
            const card = document.createElement('div');
            card.className = 'cafe-card';
            card.innerHTML = `<h2>${cafe.name}</h2><p class="address">${cafe.address}</p>`;
            cafeListContent.appendChild(card);
        });
    };

    // --- イベントリスナー ---
    // マップの表示範囲が変わって操作が止まったら（idle）、再検索ボタンを表示
    map.addListener('idle', () => {
        searchBtn.style.display = 'block';
    });
    searchBtn.addEventListener('click', fetchAndDisplayCafes);

    // --- 初期化 ---
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map.setCenter(pos);
            map.setZoom(15);
            new google.maps.Marker({
                position: pos,
                map: map,
                title: "あなたの現在地",
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