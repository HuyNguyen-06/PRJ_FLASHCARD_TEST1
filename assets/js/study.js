/*
==================================================
MODULE: HỌC FLASHCARD

FILE: study.js

MỤC ĐÍCH:
- Hiển thị từng Card
- Xem đáp án (flip card)
- Đánh dấu Biết / Chưa biết
- Đếm correct / wrong
- Tính phần trăm cuối phiên
- Hiển thị progress indicator (thanh tiến độ)
- Chống double-click khi chấm điểm

DỮ LIỆU NHẬN TỪ PHP:
- studyCards
- studySetId
- studyIsLoggedIn

LƯU Ý:
- File này CHƯA và SẼ KHÔNG tự lưu vào study_history.
- File này chỉ đếm total/correct/wrong rồi fetch() sang
  progress/save_result.php. Việc INSERT database thuộc
  trách nhiệm module progress/ (đúng ranh giới module).
==================================================
*/


// Card hiện tại đang học.
let currentIndex = 0;


// Số câu người dùng đánh dấu "Biết".
let correct = 0;


// Số câu "Chưa biết".
let wrong = 0;


// Cờ để chống double-click / double-submit.
//
// Khi người dùng vừa bấm Biết/Chưa biết, ta khóa ngay
// lập tức để một cú click (hoặc double-click) không thể
// đếm 2 lần cho cùng một Card.
let isProcessingAnswer = false;


// Card đã được lật (đã xem đáp án) hay chưa.
let isAnswerShown = false;


// Lấy các phần tử HTML.
const progressText =
    document.getElementById("study-progress");

const progressFill =
    document.getElementById("study-progress-fill");

const questionText =
    document.getElementById("study-question");

const answerText =
    document.getElementById("study-answer");

const flipCard =
    document.getElementById("flip-card");

const showAnswerBtn =
    document.getElementById("show-answer-btn");

const resultButtons =
    document.getElementById("result-buttons");

const correctBtn =
    document.getElementById("correct-btn");

const wrongBtn =
    document.getElementById("wrong-btn");

const studyResult =
    document.getElementById("study-result");


// ==================================================
// HÀM: updateProgressIndicator()
//
// Cập nhật cả chữ "Câu x / y" lẫn thanh progress bar.
//
// Thanh progress bar tính theo số Card ĐÃ HOÀN THÀNH
// (currentIndex), không tính Card đang xem, vì vậy khi
// vừa vào Card đầu tiên thanh sẽ ở 0%.
// ==================================================
function updateProgressIndicator() {

    const total = studyCards.length;

    progressText.innerText =
        "Câu "
        + (currentIndex + 1)
        + " / "
        + total;

    if (progressFill) {

        const percentDone = Math.round(
            (currentIndex / total) * 100
        );

        progressFill.style.width = percentDone + "%";
    }
}


// ==================================================
// HÀM: showCard()
//
// Hiển thị Card tại vị trí currentIndex.
// ==================================================
function showCard() {

    const card = studyCards[currentIndex];


    questionText.innerText =
        card.question;


    answerText.innerText =
        card.answer;


    updateProgressIndicator();


    // Mỗi câu mới:
    // lật thẻ về mặt trước (Câu hỏi).
    isAnswerShown = false;

    if (flipCard) {

        flipCard.classList.remove("is-flipped");
    }


    // Hiện lại nút Xem đáp án.
    showAnswerBtn.style.display = "inline-block";

    showAnswerBtn.disabled = false;


    // Ẩn nút Biết / Chưa biết.
    resultButtons.style.display = "none";


    // Mở khóa cho Card mới,
    // sẵn sàng nhận câu trả lời tiếp theo.
    isProcessingAnswer = false;

    correctBtn.disabled = false;
    wrongBtn.disabled = false;
}


// ==================================================
// HÀM: revealAnswer()
//
// Lật thẻ sang mặt Đáp án và hiện nút Biết/Chưa biết.
//
// Dùng chung cho cả:
// - bấm nút "Xem đáp án"
// - bấm trực tiếp vào thẻ (flip card)
// ==================================================
function revealAnswer() {

    if (isAnswerShown) {
        return;
    }

    isAnswerShown = true;

    if (flipCard) {

        flipCard.classList.add("is-flipped");
    }

    showAnswerBtn.style.display = "none";

    resultButtons.style.display = "block";
}


// ==================================================
// Khi bấm "Xem đáp án"
// ==================================================
showAnswerBtn.addEventListener(
    "click",
    function () {

        revealAnswer();
    }
);


// ==================================================
// Khi bấm trực tiếp vào thẻ (flip card)
//
// Chỉ lật khi đáp án chưa hiện. Sau khi đáp án đã hiện,
// bấm vào thẻ sẽ không làm gì (tránh lật lại lộn xộn khi
// người học đang chọn Biết/Chưa biết).
// ==================================================
if (flipCard) {

    flipCard.addEventListener(
        "click",
        function () {

            revealAnswer();
        }
    );
}


// ==================================================
// Khi người dùng bấm "Biết"
// ==================================================
correctBtn.addEventListener(
    "click",
    function () {

        // Chống double-click: nếu đang xử lý câu trả lời
        // trước đó rồi thì bỏ qua click này.
        if (isProcessingAnswer) {
            return;
        }

        isProcessingAnswer = true;

        correctBtn.disabled = true;
        wrongBtn.disabled = true;

        correct++;

        nextCard();
    }
);


// ==================================================
// Khi bấm "Chưa biết"
// ==================================================
wrongBtn.addEventListener(
    "click",
    function () {

        // Chống double-click tương tự nút "Biết".
        if (isProcessingAnswer) {
            return;
        }

        isProcessingAnswer = true;

        correctBtn.disabled = true;
        wrongBtn.disabled = true;

        wrong++;

        nextCard();
    }
);


// ==================================================
// HÀM: nextCard()
//
// Chuyển sang Card tiếp theo.
// Nếu hết Card -> hiện kết quả.
// ==================================================
function nextCard() {

    currentIndex++;


    if (currentIndex < studyCards.length) {

        showCard();

    } else {

        finishStudy();
    }
}


// ==================================================
// HÀM: finishStudy()
//
// Hiển thị kết quả cuối phiên.
// ==================================================
function finishStudy() {

    const total = studyCards.length;

    const percent = Math.round(
        (correct / total) * 100
    );


    // Ẩn khu vực học.
    document.querySelector(
        ".study-wrapper"
    ).style.display = "none";


    // Đưa progress bar về 100% trước khi ẩn,
    // để nếu người dùng thấy thoáng qua thì vẫn hợp lý.
    if (progressFill) {

        progressFill.style.width = "100%";
    }


    // Hiện kết quả.
    studyResult.style.display = "block";


    document.getElementById(
        "total-result"
    ).innerText = total;


    document.getElementById(
        "correct-result"
    ).innerText = correct;


    document.getElementById(
        "wrong-result"
    ).innerText = wrong;


    document.getElementById(
        "percent-result"
    ).innerText = percent;

    saveStudyResult(total);

    /*
    Kết quả (set_id, total, correct, wrong) được gửi sang
    progress/save_result.php bằng fetch() trong hàm
    saveStudyResult() bên dưới.

    Module Cards KHÔNG tự INSERT vào study_history — việc
    ghi database thuộc về module progress/ (đúng ranh giới
    module đã thống nhất trong docs/01_TEAM_MAP.md).
    */
}


// Khi trang vừa load,
// hiển thị Card đầu tiên.
//
// studyCards luôn có ít nhất 1 phần tử tại thời điểm này,
// vì study.php đã chặn và hiển thị màn hình riêng khi
// bộ Flashcard chưa có Card nào (không render script này).
showCard();

// ==================================================
// HÀM: saveStudyResult()
//
// Mục đích:
// - Nếu đã login:
//   gửi kết quả sang progress/save_result.php
//
// - Nếu là Guest:
//   không lưu database
// ==================================================
function saveStudyResult(total) {

    const saveMessage =
        document.getElementById("save-message");


    /*
    Guest vẫn được học.

    Nhưng vì không có user_id,
    hệ thống không thể lưu lịch sử cá nhân.
    */
    if (!studyIsLoggedIn) {

        saveMessage.innerHTML =
            'Bạn đang học với tư cách Guest. '
            + '<a href="/PRJ_FLASHCARD/auth/login.php">'
            + 'Đăng nhập'
            + '</a>'
            + ' để lưu tiến độ.';

        return;
    }


    /*
    FormData dùng để tạo dữ liệu POST.

    Dữ liệu gửi:
    - set_id
    - total
    - correct
    - wrong
    */
    const formData = new FormData();

    formData.append(
        "set_id",
        studySetId
    );

    formData.append(
        "total",
        total
    );

    formData.append(
        "correct",
        correct
    );

    formData.append(
        "wrong",
        wrong
    );


    saveMessage.innerText =
        "Đang lưu kết quả...";


    /*
    Gửi dữ liệu sang PHP
    mà không reload trang.
    */
    fetch(
        "/PRJ_FLASHCARD/progress/save_result.php",
        {
            method: "POST",
            body: formData
        }
    )

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            saveMessage.innerText =
                "Đã lưu kết quả học.";

        } else {

            saveMessage.innerText =
                data.message;
        }

    })

    .catch(error => {

        console.error(error);

        saveMessage.innerText =
            "Có lỗi khi lưu kết quả học.";
    });
}
