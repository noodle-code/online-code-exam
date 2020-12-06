# Refactoring Exam

The presented code makes use of a good design pattern (Repository Pattern) to create a readable and maintainable code. It also made use of the parent classes to avoid redundancy of methods in child classes. However, there are some aspects that needs review and refactoring. Below are listed good points of the code as well as the points that needs attention. 

### Good points
- Application uses a repository pattern.
- Some of the methods are short, isolated, and does a single purpose, especially on parent classes.

### Not so good points
- Repository methods are too long and hard to read.
- Not taking advantage of array argument options for some class methods.
- Could have made use of constants.
- Should have been consistent with the utilization of dependencies. E.g Some instances of using Date when Carbon is available.
- Documentation comments contains parameters and return information but lacks short description of the method it self.
- Some class methods lack default return, e.g. 404.
- Unnecessary commented lines of code.
- Unused variables.
- Some variables are defined but used as a return data immediately.
- Some controller codes still contains repository logics.
- Some repository methods can be simplified to provide more descriptive and shorter lines for controller.
- Optional parameters does not have any default values.
- Casing are not consistent. Some variables use snake_case or camelCase.
- Some variables are not descriptive.
- Maybe laravel localization could have been used.
- Some defined methods are not used. Could be deprecated methods that needs deletion.
- Could have taken advantage of some Eloquent methods. E.g. findOrFail.
- Could have transferred some query related logic to the model class.


As per the instruction, I have did not dwell too long to this exam and thus the refactoring is totally not complete. However, all of the ideas of what should be considered for refactoring are included in the list.

Thank you for opportunity!