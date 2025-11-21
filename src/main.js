import "./style11.css";
import products from "../api/products.json"
console.log(products);
import { showProductcontainer } from "./homeProductsCards";

showProductcontainer(products);