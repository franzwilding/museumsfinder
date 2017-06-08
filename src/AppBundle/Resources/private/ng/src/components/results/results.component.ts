import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'results',
  templateUrl: './results.component.html'
})
export class Results {

  constructor(private data: Data) {
  }
}