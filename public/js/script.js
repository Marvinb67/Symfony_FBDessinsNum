let img_slider = document.getElementsByClassName('img_slider')
console.log(img_slider)


let step = 0

let suivant = document.querySelector('.suivant')

console.log(suivant)

let precedent = document.getElementsByClassName('precedent')


let nb_img = img_slider.length

console.log(nb_img)

function enleverActiveImg() {
    for(let i = 0; i < nb_img; i++){
        img_slider[i].classList.remove('active')
    }
}

suivant.addEventListener('click', function() {
    step++
    console.log(img_slider[step])
    enleverActiveImg()
    img_slider[step].classList.add('active')
})

