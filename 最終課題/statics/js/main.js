// --- 最終課題/statics/js/main.js ---

// このinitMap関数は、Google Maps APIのスクリプトから自動的に呼び出されます
function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error("マップを描画するための div(#map) が見つかりません。");
        return;
    }

    // 東京駅を中心とした地図を作成
    const tokyoStation = { lat: 35.6812, lng: 139.7671 };
    const map = new google.maps.Map(mapElement, {
        zoom: 13,
        center: tokyoStation,
        mapId: 'TOKYO_CAFE_MAP' // マップIDを指定すると、よりモダンな地図になります
    });

    // カフェのデータをループしてマーカーを設置
    if (typeof cafes !== 'undefined' && Array.isArray(cafes)) {
        cafes.forEach(cafe => {
            if (cafe.latitude && cafe.longitude) {
                const position = {
                    lat: parseFloat(cafe.latitude),
                    lng: parseFloat(cafe.longitude)
                };

                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: cafe.name,
                });

                // マーカークリック時に情報ウィンドウを表示
                const infowindow = new google.maps.InfoWindow({
                    content: `<div><strong>${cafe.name}</strong><br>${cafe.address}</div>`,
                });

                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });
            }
        });
    }

    // 現在地を取得して表示
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userPosition = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                // 地図の中心を現在地に移動
                map.setCenter(userPosition);
                map.setZoom(15);
                // 現在地に特別なマーカーを設置
                new google.maps.Marker({
                    position: userPosition,
                    map: map,
                    title: "あなたの現在地",
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 7,
                        fillColor: "#4285F4",
                        fillOpacity: 1,
                        strokeColor: "white",
                        strokeWeight: 2,
                    },
                });
            },
            () => {
                console.warn("現在地の取得に失敗しました。");
            }
        );
    }
}