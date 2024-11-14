const matrixContainer = document.querySelector('.matrix-container');
const columns = Math.floor(window.innerWidth / 20); 
const drops = Array(columns).fill(0); 

function createMatrix() {
    const matrixHTML = Array.from({ length: columns }).map((_, index) => {
        return `<div class="column" style="left: ${index * 20}px;">${String.fromCharCode(33 + Math.random() * 122)}</div>`;
    }).join('');

    matrixContainer.innerHTML = matrixHTML;
}

function rain() {
    const columns = document.querySelectorAll('.column');
    columns.forEach((column, index) => {
        const charCode = Math.floor(Math.random() * 122) + 33;
        column.innerHTML = String.fromCharCode(charCode);
        
        drops[index]++;

        // Sütun 500px'e ulaştığında sıfırla ama kaybolmasına izin verme
        if (drops[index] * 20 > window.innerHeight) {
            drops[index] = 0; // Sıfırla
        }

        column.style.top = `${drops[index] * 20}px`;
    });
}

function randomStart() {
    for (let i = 0; i < drops.length; i++) {
        drops[i] = Math.floor(Math.random() * 25);
    }
}

createMatrix();
randomStart(); 
setInterval(rain, 30); // Her 100ms'de bir yağmur animasyonu
