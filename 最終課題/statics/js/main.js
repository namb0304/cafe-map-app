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
            // 緯度経度がないデータはスキップ
            if (!cafe.latitude || !cafe.longitude) return;

            // マーカーを作成
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(cafe.latitude), lng: parseFloat(cafe.longitude) },
                map: map,
                title: cafe.name,
                animation: google.maps.Animation.DROP // マーカーが上から落ちてくるアニメーション
            });

            // ================================================================
            // ★★★ ここがピンのクリックイベントとボタンを作成する核心部分 ★★★
            // ================================================================
            // 吹き出し(InfoWindow)の中身となるHTML文字列を作成
            let popupContent = `<div class="map-popup"><b>${cafe.name}</b>`;
            
            // 詳細ページへのリンクボタン
            popupContent += `<a href="cafe_details.php?id=${cafe.id}">詳細とレビューを見る</a>`;
            
            // 公式サイトURLがDBにあれば、公式サイトへのリンクボタンも追加
            if (cafe.url) { 
                popupContent += `<a href="${cafe.url}" target="_blank" rel="noopener noreferrer">公式サイト</a>`;
            }
            popupContent += `</div>`;
            
            // 吹き出し(InfoWindow)オブジェクトを作成
            const infowindow = new google.maps.InfoWindow({ content: popupContent });

            // マーカーに「クリックされたら」というイベントを追加
            marker.addListener('click', () => {
                // もし他の吹き出しが開いていたら、それを閉じる
                if (currentInfoWindow) {
                    currentInfoWindow.close();
                }
                // 新しい吹き出しを開く
                infowindow.open(map, marker);
                // 開いた吹き出しを「現在開いているもの」として記録
                currentInfoWindow = infowindow;
            });
            // ================================================================

            // 作成したマーカーを管理用配列に追加
            markers.push(marker);

            // 右側のリストにもカフェ情報をカードとして追加
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
                new google.maps.Marker({ position: pos, map: map, title: "あなたの現在地" });
                fetchAndDisplayCafes(); // 現在地取得後、自動で周辺のカフェを検索
            },
            () => {
                // 現在地取得に失敗した場合は、初期位置（東京駅）で検索
                console.warn("現在地の取得に失敗しました。初期位置で検索します。");
                fetchAndDisplayCafes();
            }
        );
    } else {
        // ブラウザが位置情報に対応していない場合も、初期位置で検索
        console.warn("お使いのブラウザは位置情報機能に対応していません。");
        fetchAndDisplayCafes();
    }
}