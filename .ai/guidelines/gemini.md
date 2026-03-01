<laravel-boost-guidelines>

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- When creating a new controller, except otherwise stated, always create an invokable controller.
- For controller validation, always create a Form Request class in the appropriate namespace. For example, if the Controller is in \App\Http\Controllers\Admin\Auth, then the Form Request should be in \App\Http\Requests\Admin\Auth.
- When getting a form request object value in a controller action use $request->safe()->string('property_name'). Use the appropriate method for the type of value you are getting. eg array(), inetger() etc
- When creating a new model, always create a repository class in the appropriate namespace. For example, if the Model is in \App\Models, then the Repository should be in \App\Repositories.
- When creating an object of a model, use $object = new Model(); $object->property = value; $object->save();
- In Controller actions, use the repository of the model to create, update, delete etc. Read model directly without using the repository. If the respective repository or method does not exist, create it.
- When creating a notification, do not implement ShouldQueue. Also, the class name should end with Notification. E.g., VerificationNotification.
- Always use array in the rules definition of Form Request fields to validate the request data. eg: 'email' => ['required', 'email:dns,spoof,rfc', 'max:255']
- Always simplify FQN and use the import statement what calling a class, interface, trait etc. Always use the import statement what calling a class, interface, trait etc. Arrange the import statements in alphabetical order.
- When creating a new controller, except otherwise stated, always create an invokable controller and put it in the appropriate namespace.

### Type Declarations

- Always use appropriate PHP type hints for method parameters.

## Comments
- Prefer PHPDoc blocks over comments. Use comments within the code itself when there is something complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

</laravel-boost-guidelines>
