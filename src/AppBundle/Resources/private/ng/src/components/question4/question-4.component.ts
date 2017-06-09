import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'question-4',
  templateUrl: './question4.component.html'
})
export class Question4 {

  constructor(private data: Data) {}

  change(value) : void {
    this.data.uniqueness = value;
  }
}