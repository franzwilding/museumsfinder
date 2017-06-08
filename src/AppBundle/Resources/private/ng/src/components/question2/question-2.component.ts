import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'question-2',
  templateUrl: './question2.component.html'
})
export class Question2 {

  districtNames : string[] = [
    '',
    '1. Innere Stadt',
    '2. Leopoldstadt',
    '3. Landstraße',
    '4. Wieden',
    '5. Margareten',
    '6. Mariahilf',
    '7. Neubau',
    '8. Josefstadt',
    '9. Alsergrund',
    '10. Favoriten',
    '11. Simmering',
    '12. Meidling',
    '13. Hietzing',
    '14. Penzing',
    '15. Fünfhaus',
    '16. Ottakring',
    '17. Hernals',
    '18. Währing',
    '19. Döbling',
    '20. Brigittenau',
    '21. Floridsdorf',
    '22. Donaustadt',
    '23. Liesing'
  ];
  districtNumbers : number[] = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23];

  constructor(private data: Data) {}

  availableDistrictNumbers() : number[] {
    return this.districtNumbers.filter(d => { return !this.isSelected('' + d); });
  }

  orderedSelectedDistricts() : string[] {
    return this.data.districts.sort((a, b) => { return parseInt(a) - parseInt(b)});
  }

  toggle(district) : void {
    if(this.isSelected('' + district)) {
      this.data.districts.splice(this.data.districts.indexOf('' + district), 1);
    } else {
      this.data.districts.push('' + district);
    }
  }

  isSelected(district : string) : boolean {
    return this.data.districts.indexOf('' + district) >= 0;
  }

  fillFor(district) {
    return this.isSelected(district) ? '#7ED321' : 'transparent';
  }

  selectChanged(event) : void {
    if(event.target.value != '+' && !this.isSelected('' + event.target.value)) {
      this.data.districts.push('' + event.target.value);
    }
  }

  removeDistrict(district) : void {
    this.data.districts.splice(this.data.districts.indexOf('' + district), 1);
  }
}