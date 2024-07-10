document.addEventListener('DOMContentLoaded', function() {
    const dateLinks = document.querySelectorAll('.date-link');
    let selectedDateElement = null; // 現在選択されている日付要素を保持

    // 各日付リンクにクリックイベントを追加
    dateLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();

            const selectedRoom = this.getAttribute('data-room'); // クリックされたリンクから部屋を取得
            const selectedDate = this.getAttribute('data-date'); // クリックされたリンクから日付を取得

            // 選択されている日付要素の背景色をクリア
            if (selectedDateElement) {
                selectedDateElement.classList.remove('selected-date');
            }

            // 新しいクリックされた日付に背景色を追加
            this.classList.add('selected-date');
            selectedDateElement = this;

            // 関数を呼び出し予約情報を反映
            reflection(selectedRoom, selectedDate);
        });
    });

    // 各面談室のラジオボタンにクリックイベントを追加
    const tabItems = document.querySelectorAll('input[name="tab_item"]');
    tabItems.forEach(tabItem => {
        tabItem.addEventListener('click', function() {
            const selectedRoom = this.value; // クリックされた面談室の値を取得
            const selectedDate = selectedDateElement ? selectedDateElement.getAttribute('data-date') : null; // 選択されている日付を取得
            if (selectedDateElement && selectedDate) {
                // 日付要素にクラスを追加して背景色を更新
                const dateElement = document.querySelector(`.date-link[data-date="${selectedDate}"]`);
                if (dateElement) {
                    dateElement.classList.add('selected-date');
                    selectedDateElement = dateElement; // 日付要素を更新
                }
                reflection(selectedRoom, selectedDate); // 予約情報を反映
            }
            
            // 選択された面談室をモーダルウィンドウに表示
            const reservationRoom = document.getElementById("reservation-room");
            reservationRoom.textContent = selectedRoom;
        });
    });
    // 予約情報を取得しUIに反映する関数
    function reflection(selectedRoom, selectedDate) {
        fetch('../php/reflection.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ selectedRoom: selectedRoom, selectedDate: selectedDate })
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('データの取得に失敗しました');
            }
            return response.json();
        })
        .then(function(data) {
            if (data.error) {
                throw new Error(data.error);
            }

            let reservations = Array.isArray(data) ? data : [];

            // 空き状況ラベル要素を取得
            let labels = document.querySelectorAll('.situation label');
            labels.forEach(label => {
                let labelTime = label.getAttribute('data-value'); // ラベルの時間を取得
                let span = label.querySelector('.add');

                // 予約済みかどうかをチェック
                let isReserved = reservations.some(reservation => {
                    return areDatesSame(new Date(reservation.date), new Date(selectedDate)) &&
                        labelTime === reservation.start_time &&
                        reservation.room_name === selectedRoom;
                });

                // 空き状況ラベルを更新
                if (isReserved) {
                    span.textContent = '✕'; // 予約済みの場合
                    label.classList.add('disabled'); // ラジオボタン無効化
                } else {
                    span.textContent = '◯'; // 予約可能の場合
                    label.classList.remove('disabled'); // ラジオボタン有効化
                }
            });

            // 予約ラジオボタン要素を取得
            let radioLabels = document.querySelectorAll('.addmin label');
            radioLabels.forEach(radioLabel => {
                let radioTime = radioLabel.querySelector('input[type="radio"]').value.split('～')[0] + ':00'; // ラジオボタンの時間を取得
                let radioInput = radioLabel.querySelector('input[type="radio"]');

                // 予約済みかどうかをチェック
                let isReserved = reservations.some(reservation => {
                    return areDatesSame(new Date(reservation.date), new Date(selectedDate)) &&
                        radioTime === reservation.start_time &&
                        reservation.room_name === selectedRoom;
                });

                // ラジオボタンの無効化を設定
                radioInput.disabled = isReserved;
            });
        })
        .catch(function(error) {
            console.error('エラー:', error);
        });
    }

    // 日付を比較する
    function areDatesSame(date1, date2) {
        return date1.getFullYear() === date2.getFullYear() &&
            date1.getMonth() === date2.getMonth() &&
            date1.getDate() === date2.getDate();
    }

    // フォームを非表示にする
    let form = document.querySelector("form");
    form.style.display = "none";

    // カレンダーの日付リンクにクリックイベントを追加
    let dates = document.querySelectorAll(".calendar a");
    dates.forEach(function(date) {
        date.addEventListener("click", function(event) {
            event.preventDefault();
            form.style.display = "block"; // フォームを表示

            let clickedDate = this.textContent; // クリックされた日付を取得
            let yearMonth = document.querySelector("h3").textContent.trim(); // 年月を取得

            // 年月日を分解して取得
            let parts = yearMonth.split("年");
            let year = parseInt(parts[0]);
            parts = parts[1].split("月");
            let month = parseInt(parts[0]) - 1; // 月は0から始まる
            let day = parseInt(clickedDate);

            // 日付と曜日を取得
            let selectedDate = new Date(year, month, day);
            let weekdays = ["日", "月", "火", "水", "木", "金", "土"];
            let weekday = weekdays[selectedDate.getDay()];

            // h2の日時を変更
            let timeHeader = document.querySelector("h2");
            timeHeader.textContent = `${year}年${month + 1}月${day}日 (${weekday}曜日)`;

            // クリックされた日付をモーダルウィンドウにも反映
            let reservationDate = document.getElementById("reservation-date");
            reservationDate.textContent = `${year}年${month + 1}月${day}日 (${weekday}曜日)`;

            // 予約情報を取得して空き状況を更新
            reflection(document.querySelector('input[name="tab_item"]:checked').value, selectedDate);

            event.stopPropagation(); // イベントのバブリングを停止
        });
    });

    // フォームがクリックされた時
    form.addEventListener("click", function(event) {
        event.stopPropagation(); // イベントのバブリングを停止
    });

    // モーダルウィンドウを表示するためのボタン要素を取得
    let openModal = document.getElementById("openModal");

    // モーダルウィンドウが開かれた時
    openModal.addEventListener("click", function(event) {
        let selectedTime = document.querySelector('input[name="time"]:checked');
        if (!selectedTime) {
            alert("予約時間を選択してください");
            return; // 時間が選択されていない場合モーダルを開かない
        }

        // モーダルウィンドウを表示
        document.getElementById('myModal').style.display = 'block';

        // モーダルに予約日付を表示
        let reservationDate = document.getElementById("reservation-date");
        reservationDate.textContent = document.getElementById("reservation-date").textContent;

        // モーダルに面談室を表示
        let reservationRoom = document.getElementById("reservation-room");
        reservationRoom.textContent = document.querySelector('input[name="tab_item"]:checked + label.tab_item').textContent;

        // モーダルに予約時間を表示
        let reservationTime = document.getElementById("reservation-time");
        reservationTime.textContent = selectedTime.value;
    });


    // モーダルウィンドウ内の閉じるボタンの処理
    let closeModalButtons = document.getElementsByClassName('close');
    for (let i = 0; i < closeModalButtons.length; i++) {
        closeModalButtons[i].addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'none';
        });
    }

    // 日付をDateオブジェクトに変換する関数
    function parseDate(dateString) {
        let dateParts = dateString.match(/(\d{4})年(\d{1,2})月(\d{1,2})日/);
        if (dateParts) {
            let year = parseInt(dateParts[1], 10); // 年
            let month = parseInt(dateParts[2], 10) - 1; // 月
            let day = parseInt(dateParts[3], 10); // 日
            return new Date(year, month, day);
        } else {
            throw new Error('無効な日付です');
        }
    }
    
    // 予約処理
    document.getElementById('confirm').addEventListener('click', function() {
        // 予約情報を取得
        let date = document.getElementById('reservation-date').textContent;
        let selectedTime = document.querySelector('input[name="time"]:checked').value;
        
        // 選択された時間を開始時刻と終了時刻に分割
        let [startTimeStr, endTimeStr] = selectedTime.split('～');
        
        // 日付を正しい形式に変換
        try {
            let parsedDate = parseDate(date);
            // 日付の時間情報を正確な日付に設定する
            parsedDate.setHours(12, 0, 0, 0); // 12時に設定すると日付が一日前にズレる

            // 面談室を取得
            let roomElement = document.querySelector('input[name="tab_item"]:checked');
            let room_name = roomElement ? roomElement.value : null;
        
            // コメントを取得
            let commentElement = document.querySelector('textarea[name="comment"]');
            let comment = commentElement ? commentElement.value : null;
        
            // フォームデータを作成して取得したデータを追加
            let formData = new FormData();
            formData.append('date', parsedDate.toISOString().split('T')[0]); // YYYY-MM-DD形式に変換
            formData.append('start_time', startTimeStr);
            formData.append('end_time', endTimeStr);
            formData.append('room_name', room_name);
            formData.append('comment', comment);
        
            // PHPへPOSTリクエスト
            fetch('../php/reserve.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (response.ok) {
                    alert('予約が完了しました！');
                    document.getElementById('myModal').style.display = 'none';
                } else if (response.status === 400) {
                    response.json().then(data => {
                        alert(data.error);
                    });
                } else {
                    alert('サーバーエラーが発生しました');
                }
            })
            .catch(error => {
                console.error('リクエストエラー:', error);
                alert('エラーが発生しました');
            });
        } catch (error) {
            console.error(error.message);
            alert('日付の変換中にエラーが発生しました');
        }
    });
});