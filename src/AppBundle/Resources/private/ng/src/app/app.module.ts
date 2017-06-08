import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { AppComponent } from './app.component';
import { Data } from './data';

import { Question1 } from '../components/question1/question-1.component';
import { Question2 } from '../components/question2/question-2.component';
import { Question3 } from '../components/question3/question-3.component';
import { Question4 } from '../components/question4/question-4.component';
import { Question5 } from '../components/question5/question-5.component';

import { Paginator } from '../components/paginator/paginator.component';
import { Results } from '../components/results/results.component';

@NgModule({
  declarations: [
    AppComponent,
    Question1,
    Question2,
    Question3,
    Question4,
    Question5,
    Paginator,
    Results
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule
  ],
  providers: [
    Data
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
