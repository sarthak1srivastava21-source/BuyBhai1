const productContainer = document.querySelector("#productContainer");
const productTemplate = document.querySelector("#productTemplate");

export const showProductcontainer = (products)  => {
    if(products) {
        return false;
    }

    products.forEach((curProd) => {
        const { brand , category , description , id , image , name , price, stock } =
        curProd;

        const productClone = document.importNode(productTemplate.content, true);


        productClone.querySelector('.productName').content = name;


        productContainer.append(productClone);
    });
};