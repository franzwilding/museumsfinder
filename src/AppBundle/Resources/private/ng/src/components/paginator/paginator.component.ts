import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'paginator',
  templateUrl: './paginator.component.html'
})
export class Paginator {

  count : number = 5;
  items : number[] = [];

  constructor(private data: Data) {
    for(let i = 0; i < this.count; i++) {
      this.items.push(i);
    }
  }

  goTo(index) : void {
    this.data.currentQuestion = index + 1;
  }

  itemClass(cur) : string {
    return (cur == this.data.currentQuestion) ? 'active' : '';
  }
}