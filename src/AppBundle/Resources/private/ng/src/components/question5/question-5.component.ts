import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'question-5',
  templateUrl: './question5.component.html'
})
export class Question5 {

  constructor(private data: Data) {}

  change(event) : void {
    this.data.searchText = event.target.value;
  }
}