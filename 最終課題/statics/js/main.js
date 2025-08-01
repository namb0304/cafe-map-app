// ファイルパス: /statics/js/main.js

/**
 * Google Maps APIの読み込みが完了したときに自動的に呼ばれるメイン関数
 */
function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error("マップを描画する 'map' というIDの要素が見つかりません。");
        return;
    }

    // マップの初期化
    const map = new google.maps.Map(mapElement, {
        center: { lat: 35.6812, lng: 139.7671 }, // 初期中心地：東京駅
        zoom: 13,
        mapTypeControl: false,
        streetViewControl: false
    });

    const searchBtn = document.getElementById('search-this-area-btn');
    const cafeListContent = document.getElementById('cafe-list-content');
    let markers = []; // マップ上のマーカーを管理する配列
    let currentInfoWindow = null; // 現在開いている情報ウィンドウを管理

    /**
     * 表示範囲内のカフェ情報をサーバーから取得して表示する非同期関数
     */
    const fetchAndDisplayCafes = async () => {
        const bounds = map.getBounds();
        if (!bounds) return;

        const ne = bounds.getNorthEast();
        const sw = bounds.getSouthWest();

        searchBtn.textContent = '検索中...';
        searchBtn.disabled = true;

        try {
            // APIにリクエストを送り、カフェ情報を取得
            const response = await fetch(`api/get_cafes.php?sw_lat=${sw.lat()}&sw_lng=${sw.lng()}&ne_lat=${ne.lat()}&ne_lng=${ne.lng()}`);
            if (!response.ok) {
                throw new Error(`サーバーからの応答エラー: ${response.statusText}`);
            }
            
            const cafes = await response.json();
            updateMapAndList(cafes); // 受け取ったデータでマップとリストを更新

        } catch (error) {
            console.error('カフェ情報の取得に失敗しました:', error);
            cafeListContent.innerHTML = '<p>カフェ情報の取得に失敗しました。時間をおいて再度お試しください。</p>';
        } finally {
            searchBtn.textContent = 'このエリアで再検索';
            searchBtn.disabled = false;
            searchBtn.style.display = 'none';
        }
    };

    /**
     * マップとリストを最新のカフェ情報で更新する関数
     * @param {Array} cafes - サーバーから受け取ったカフェ情報の配列
     */
    const updateMapAndList = (cafes) => {
        // 1. 古いマーカーを地図から削除
        markers.forEach(marker => marker.setMap(null));
        markers = [];
        cafeListContent.innerHTML = '';

        if (cafes.length === 0) {
            cafeListContent.innerHTML = '<p>このエリアにはカフェが見つかりませんでした。</p>';
            return;
        }

        // 2. 新しいカフェ情報でマーカーとリストを作成
        cafes.forEach(cafe => {
            if (!cafe.latitude || !cafe.longitude) return;

            const marker = new google.maps.Marker({
                position: { lat: parseFloat(cafe.latitude), lng: parseFloat(cafe.longitude) },
                map: map,
                title: cafe.name,
                animation: google.maps.Animation.DROP
            });

            let popupContent = `<div class="map-popup"><b>${cafe.name}</b>`;
            popupContent += `<a href="cafe_details.php?id=${cafe.id}">詳細とレビューを見る</a>`;
            if (cafe.url) { 
                popupContent += `<a href="${cafe.url}" target="_blank" rel="noopener noreferrer">公式サイト</a>`;
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

            const card = document.createElement('div');
            card.className = 'cafe-card';
            card.innerHTML = `<h2><a href="cafe_details.php?id=${cafe.id}">${cafe.name}</a></h2><p class="address">${cafe.address}</p>`;
            cafeListContent.appendChild(card);
        });
    };

    // 地図の操作が終わったら「このエリアで再検索」ボタンを表示
    map.addListener('idle', () => { searchBtn.style.display = 'block'; });
    
    // ボタンがクリックされたらカフェを検索
    searchBtn.addEventListener('click', fetchAndDisplayCafes);

    // 最初にページが読み込まれた時に、現在地を取得して検索を実行
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                map.setCenter(pos);
                map.setZoom(15);
                
                // ▼▼▼ ここを青色のピンに変更しました ▼▼▼
                new google.maps.Marker({ 
                    position: pos, 
                    map: map, 
                    title: "あなたの現在地",
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE, // 円形
                        scale: 8, // 大きさ
                        fillColor: "#e95423ff", // 塗りつぶしの色
                        fillOpacity: 1,
                        strokeColor: "white", // 枠線の色
                        strokeWeight: 2 // 枠線の太さ
                    }
                });
                
                fetchAndDisplayCafes();
            },
            () => {
                console.warn("現在地の取得に失敗しました。初期位置で検索します。");
                fetchAndDisplayCafes();
            }
        );
    } else {
        console.warn("お使いのブラウザは位置情報機能に対応していません。");
        fetchAndDisplayCafes();
    }
}
