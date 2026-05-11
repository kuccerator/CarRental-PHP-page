

function* szamlalo() {
    let i = 1;
    while (true) {
        yield i;  // Kiírja az aktuális i értéket és megáll
        i++;      // Növeli az i értékét a következő lépéshez
    }
}

const generator = szamlalo();  // Generátor létrehozása

console.log(generator.next().value); // 1
console.log(generator.next().value); // 2
console.log(generator.next().value); // 3
console.log(generator.next().value); // 4
