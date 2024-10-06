## Assumptions

- A customer can only subscribe to one product at a time, regardless of the subscription type.
- If the customer's subscription is expired, they cannot subscribe to the same product. Instead, their subscription will be renewed.
- A bill will not be printed until the subscription has ended.


## Design desicion

### Using namespaces and composer autoloading 
- Better project structure.
- Gives better files and classes organization.
- Avoid using require and import and only use what needed.

### Seperation of concerns
- **Models**: to ensure that each model is representing and managing the data relevant to a specific entity.

- **Enums**: to enhance readabilty and maintability.

- **Services**: to seperate the business login into service classes, to improve readability, testability and extendability.

### Single Responsibility Principle
- Each class has a single resposibility
