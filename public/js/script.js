let img_slider = document.getElementsByClassName('img_slider')
console.log(img_slider)

let etape = 0

let suivant = document.querySelector('.suivant')

console.log(suivant)

let precedent = document.getElementsByClassName('precedent')


let nb_img = img_slider.length

function enleverActiveImg() {
    for(let i = 0; i < nb_img; i++){
        img_slider[i].classList.remove('active')
    }
}

suivant.addEventListener('click', function() {
    etape++
    enleverActiveImg()
    img_slider[etape].classList.add('active')
})

