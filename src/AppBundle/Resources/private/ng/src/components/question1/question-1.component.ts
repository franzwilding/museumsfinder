import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'question-1',
  templateUrl: './question1.component.html'
})
export class Question1 {

  constructor(private data: Data) {}
}