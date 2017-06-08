import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'paginator',
  templateUrl: './paginator.component.html'
})
export class Paginator {

  items : number[] = [];

  constructor(private data: Data) {
    for(let i = 0; i < this.data.countQuestions; i++) {
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